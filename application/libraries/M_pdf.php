<?php
  if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'/third_party/mpdf/mpdf.php';

class M_pdf {

    public $param;
    public $pdf;
    public function __construct($param = array('', 'A4', 0, '', 15, 15, 18, 18, 9, 9, 'P'))
    {
        $this->param = $param;
        $this->pdf = new mPDF(...$this->param);
    }
}