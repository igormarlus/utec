# Addendum de pesquisa de keywords — 2026-07-14

Continuação de [seo-geo-expansao-keywords-2026-06-02.md](seo-geo-expansao-keywords-2026-06-02.md), usando consulta direta à API de autocomplete do Google (sem ferramenta paga):

```
curl "https://www.google.com/complete/search?client=firefox&hl=pt-BR&gl=br&q=<termo urlencoded>"
```

---

## 1. Especialidades sem landing (executado nesta sessão)

Ginecologia, Pediatria, Psiquiatria e Fonoaudiologia tinham demanda confirmada (`sistema para pediatra`, `sistema para psiquiatras`, `sistema fonoaudiologia`, `sistema ginecologia`) e já contavam com campos de prontuário mapeados no CLAUDE.md §17.5, mas nenhuma landing comercial. **4 páginas criadas e publicadas nesta sessão**: `/sistema-para-ginecologia`, `/sistema-para-pediatria`, `/sistema-para-psiquiatria`, `/sistema-para-fonoaudiologia`. Adicionadas ao sitemap e indexação solicitada no Search Console.

## 2. Concorrentes novos com sinal de busca real

O plano de offpage (jun/2026) só mapeava Feegow, Odontoclinic, Shosp e Clínica nas Nuvens para páginas `/alternativa-X`. Testando outros nomes conhecidos do mercado brasileiro de gestão clínica:

| Concorrente | `sistema X` | `X preço` | `alternativa X` / `X vs` |
|---|---|---|---|
| **Amplimed** | Forte — inclusive variação local "Amplimed Chapecó" e intenção de avaliações/fotos | Sim | Sem sinal |
| **Belasis** | Sim (com intenção de avaliações/imagens) | Sim | Sem sinal |
| **Ninsaude** | Sim (mais fraco) | Sim | Sem sinal |
| Ivix, Docway, MeuPaciente | Sem sinal de autocomplete | — | — |

**Leitura:** Amplimed, Belasis e Ninsaude são concorrentes reais com volume de marca e intenção de preço, mas ainda **sem demanda comprovada por conteúdo comparativo** ("alternativa a", "vs"). Diferente de Feegow/Odontoclinic, que já têm esse padrão de busca validado no CSV original.

**Recomendação:** não criar `/alternativa-amplimed` (ou similares) agora — seria conteúdo especulativo sem keyword para sustentar. Melhor caminho: mencionar os três num artigo tipo "Comparativo de sistemas para clínicas no Brasil: o que avaliar" (formato roundup, não página 1-a-1), e reavaliar página dedicada se a demanda de "alternativa"/"vs" aparecer numa checagem futura.

## 3. Keywords funcionais testadas — sem retorno

Testei se havia demanda por keywords orientadas a funcionalidade específica (em vez de especialidade):

- `sistema de faturamento para clinica`
- `controle financeiro para clinica pequena`
- `gestao de estoque para clinica odontologica`
- `relatorio de atendimentos por profissional`

**Nenhuma retornou sugestão do autocomplete** — sinal de que ninguém busca sistema de clínica por essas frases funcionais exatas. **Recomendação:** não produzir landing/artigo dedicado a essas frases. Isso não significa que as funcionalidades não importem — só que não são a forma como o usuário pesquisa; melhor continuar cobrindo-as como seções dentro das páginas de especialidade (como já é feito) do que como conteúdo autônomo.

## 4. Método para reuso

Rodar em lote, com `sleep 1` entre chamadas (evita erro 500 por rate limit):

```bash
for termo in "termo 1" "termo 2"; do
  curl -s "https://www.google.com/complete/search?client=firefox&hl=pt-BR&gl=br&q=$(python3 -c "import urllib.parse,sys; print(urllib.parse.quote(sys.argv[1]))" "$termo")"
  sleep 1
done
```

Resultado é um array JSON; a segunda posição traz as sugestões reais (variações e ampliações do termo digitado). Ausência de sugestões (`[]`) é, em si, um dado — indica baixa ou nenhuma demanda por aquela frase exata.
