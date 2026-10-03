<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Gerador XLSX mínimo (OOXML) sem dependências: só texto (inlineStr), cabeçalho em negrito.
// Usado pela exportação de prontuário. Requer ZipArchive.
class Xlsx_simples {

	private $abas = array();

	public static function disponivel(){
		return class_exists('ZipArchive');
	}

	public function adicionar_aba($nome, array $linhas){
		$this->abas[] = array('nome' => $this->nome_aba($nome, count($this->abas) + 1), 'linhas' => $linhas);
	}

	public function gerar(){
		if(!self::disponivel() || empty($this->abas)){ return false; }
		$tmp = tempnam(sys_get_temp_dir(), 'xlsx');
		if($tmp === false){ return false; }
		$zip = new ZipArchive();
		if($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true){
			@unlink($tmp);
			return false;
		}
		$zip->addFromString('[Content_Types].xml', $this->content_types());
		$zip->addFromString('_rels/.rels', $this->rels_raiz());
		$zip->addFromString('xl/workbook.xml', $this->workbook());
		$zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbook_rels());
		$zip->addFromString('xl/styles.xml', $this->styles());
		foreach($this->abas as $i => $aba){
			$zip->addFromString('xl/worksheets/sheet'.($i + 1).'.xml', $this->sheet($aba['linhas']));
		}
		$zip->close();
		$bin = file_get_contents($tmp);
		@unlink($tmp);
		return $bin === false ? false : $bin;
	}

	public static function xml($v){
		$v = (string)$v;
		$limpo = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $v);
		if($limpo === null){
			// UTF-8 inválido: remove bytes de controle no modo byte
			$limpo = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v);
		}
		return htmlspecialchars($limpo, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}

	public static function coluna($indice){
		$letras = '';
		$n = (int)$indice + 1;
		while($n > 0){
			$resto = ($n - 1) % 26;
			$letras = chr(65 + $resto).$letras;
			$n = (int)(($n - $resto - 1) / 26);
		}
		return $letras;
	}

	private function nome_aba($nome, $n){
		$nome = preg_replace('/[\\\\\/\?\*\[\]:]/', '', (string)$nome);
		$nome = function_exists('mb_substr') ? mb_substr($nome, 0, 31, 'UTF-8') : substr($nome, 0, 31);
		return $nome !== '' ? $nome : 'Aba'.$n;
	}

	private function sheet(array $linhas){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
		$r = 0;
		foreach($linhas as $linha){
			$r++;
			$xml .= '<row r="'.$r.'">';
			$c = 0;
			foreach($linha as $valor){
				$ref = self::coluna($c).$r;
				$estilo = $r === 1 ? ' s="1"' : '';
				$xml .= '<c r="'.$ref.'" t="inlineStr"'.$estilo.'><is><t xml:space="preserve">'.self::xml($valor).'</t></is></c>';
				$c++;
			}
			$xml .= '</row>';
		}
		return $xml.'</sheetData></worksheet>';
	}

	private function content_types(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			.'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			.'<Default Extension="xml" ContentType="application/xml"/>'
			.'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			.'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
		foreach($this->abas as $i => $aba){
			$xml .= '<Override PartName="/xl/worksheets/sheet'.($i + 1).'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
		}
		return $xml.'</Types>';
	}

	private function rels_raiz(){
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			.'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			.'</Relationships>';
	}

	private function workbook(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
		foreach($this->abas as $i => $aba){
			$xml .= '<sheet name="'.self::xml($aba['nome']).'" sheetId="'.($i + 1).'" r:id="rId'.($i + 1).'"/>';
		}
		return $xml.'</sheets></workbook>';
	}

	private function workbook_rels(){
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
		$total = count($this->abas);
		foreach($this->abas as $i => $aba){
			$xml .= '<Relationship Id="rId'.($i + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.($i + 1).'.xml"/>';
		}
		$xml .= '<Relationship Id="rId'.($total + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
		return $xml.'</Relationships>';
	}

	private function styles(){
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			.'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			.'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
			.'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
			.'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
			.'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
			.'<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
			.'</styleSheet>';
	}
}
