<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pagamento da Assinatura</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ink:#132238;
            --muted:#607086;
            --line:#d7dee8;
            --panel:#ffffff;
            --bg:linear-gradient(180deg,#f8fafc 0%,#eef4f8 100%);
            --primary:#0f766e;
            --primary-strong:#115e59;
            --accent:#ea580c;
            --soft:#f8fafc;
            --ok:#166534;
            --ok-bg:#ecfdf3;
            --ok-line:#bbf7d0;
            --warn:#9a3412;
            --warn-bg:#fff7ed;
            --warn-line:#fdba74;
            --error:#991b1b;
            --error-bg:#fef2f2;
            --error-line:#fecaca;
        }
        * { box-sizing:border-box; }
        body {
            margin:0;
            font-family: Georgia, "Times New Roman", serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.12), transparent 28%),
                radial-gradient(circle at top right, rgba(234,88,12,.12), transparent 22%),
                var(--bg);
        }
        .wrap { max-width:1120px; margin:0 auto; padding:32px 18px 56px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:22px; flex-wrap:wrap; }
        .brand { font-size:14px; letter-spacing:.16em; text-transform:uppercase; font-weight:700; color:var(--primary-strong); }
        .back-link { color:var(--muted); font-size:14px; text-decoration:none; }
        .hero { display:grid; grid-template-columns:minmax(0,1fr) minmax(340px,.9fr); gap:24px; align-items:start; }
        .panel {
            background:rgba(255,255,255,.92);
            border:1px solid rgba(215,222,232,.95);
            border-radius:28px;
            box-shadow:0 24px 60px rgba(19,34,56,.08);
            padding:26px;
        }
        .eyebrow { font-size:12px; letter-spacing:.18em; text-transform:uppercase; color:var(--accent); font-weight:700; }
        h1 { margin:12px 0 10px; font-size:44px; line-height:1.04; }
        .lead { font-size:17px; line-height:1.75; color:#45556c; margin:0; }
        .summary-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-top:20px; }
        .summary-card { border:1px solid var(--line); border-radius:18px; background:#fbfdff; padding:16px; }
        .label { font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); }
        .value { font-size:24px; font-weight:700; margin-top:8px; }
        .copy { color:#4f6278; line-height:1.7; font-size:14px; }
        .alert { border-radius:16px; padding:14px 16px; font-size:14px; margin-bottom:16px; }
        .alert-ok { background:var(--ok-bg); color:var(--ok); border:1px solid var(--ok-line); }
        .alert-warn { background:var(--warn-bg); color:var(--warn); border:1px solid var(--warn-line); }
        .alert-error { background:var(--error-bg); color:var(--error); border:1px solid var(--error-line); }
        .method-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; margin-top:24px; }
        .method-card { border:1px solid var(--line); border-radius:24px; background:#fff; padding:22px; box-shadow:0 16px 38px rgba(19,34,56,.05); }
        .method-card h2 { margin:0 0 8px; font-size:30px; }
        .method-copy { font-size:15px; line-height:1.7; color:#53657a; margin:0 0 18px; }
        .btn-row { display:flex; flex-wrap:wrap; gap:12px; margin-top:16px; }
        .btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:999px;
            padding:13px 18px;
            font-size:14px;
            font-weight:700;
            text-decoration:none;
            border:0;
            cursor:pointer;
        }
        .btn-primary { background:linear-gradient(90deg,var(--primary),var(--accent)); color:#fff; }
        .btn-secondary { background:#fff; border:1px solid var(--line); color:var(--ink); }
        .btn-muted { background:#f8fafc; border:1px solid #dbe4ec; color:#334155; }
        .pix-box {
            margin-top:18px;
            border:1px dashed rgba(15,118,110,.25);
            background:linear-gradient(180deg,#f8fffe 0%,#f3fbfb 100%);
            border-radius:20px;
            padding:18px;
        }
        .pix-qr { width:min(100%,280px); display:block; margin:0 auto 14px; border-radius:16px; background:#fff; padding:10px; border:1px solid #d4e5e3; }
        .pix-code {
            width:100%;
            min-height:110px;
            border:1px solid #cfd9e5;
            border-radius:16px;
            padding:12px;
            font:inherit;
            color:var(--ink);
            background:#fff;
        }
        .status-pill {
            display:inline-flex;
            align-items:center;
            border-radius:999px;
            padding:7px 12px;
            font-size:12px;
            font-weight:700;
            letter-spacing:.04em;
            text-transform:uppercase;
            background:#e2e8f0;
            color:#475569;
        }
        .status-active { background:#dcfce7; color:#166534; }
        .status-trial { background:#dbeafe; color:#1d4ed8; }
        .status-past_due { background:#fef3c7; color:#92400e; }
        .status-canceled { background:#fee2e2; color:#b91c1c; }
        .status-paused { background:#ede9fe; color:#5b21b6; }
        .card-brick-wrap { min-height:380px; }
        .loader { color:var(--muted); font-size:14px; }
        .foot-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:24px; }
        @media (max-width: 960px) {
            .hero, .method-grid { grid-template-columns:1fr; }
        }
        @media (max-width: 720px) {
            h1 { font-size:34px; }
            .summary-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div class="brand">UTecnologia Saude</div>
            <a class="back-link" href="<?=$back_url?>">Voltar</a>
        </div>

        <div class="hero">
            <div class="panel">
                <div class="eyebrow">Pagamento da assinatura</div>
                <h1>Finalize a assinatura com PIX ou cartao.</h1>
                <p class="lead">
                    Esta etapa usa a API do Mercado Pago para cobrar o ciclo em aberto da assinatura da sua operacao.
                    Voce pode gerar um PIX com QR Code ou concluir o pagamento com cartao de credito sem sair desta jornada.
                </p>

                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">Clinica</div>
                        <div class="value"><?=$detail['tenant']->tenant_nome?></div>
                        <div class="copy">Plano <?=$detail['plano']->modelo?></div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Assinatura</div>
                        <div class="value" style="font-size:20px;"><?=$detail['subscription']->status?></div>
                        <div class="copy">Owner: <?=$detail['owner']->email?></div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Ciclo atual</div>
                        <div class="value"><?=($open_cycle && $open_cycle->reference_label ? $open_cycle->reference_label : 'Sem ciclo aberto')?></div>
                        <div class="copy">
                            <? if($open_cycle){ ?>
                                Vencimento <?=($open_cycle->due_at ? date('d/m/Y', strtotime($open_cycle->due_at)) : 'nao definido')?>
                            <? } else { ?>
                                Nenhuma cobranca pendente no momento.
                            <? } ?>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Valor a cobrar</div>
                        <div class="value">R$ <?=number_format((float)($open_cycle ? $open_cycle->amount_due : 0), 2, ',', '.')?></div>
                        <div class="copy">Metodo mais recente: <?=($detail['subscription']->checkout_type ? $detail['subscription']->checkout_type : 'nao definido')?></div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <? if($flash_ok){ ?><div class="alert alert-ok"><?=$flash_ok?></div><? } ?>
                <? if($flash_error){ ?><div class="alert alert-error"><?=$flash_error?></div><? } ?>
                <? if(!$mercadopago_ready){ ?><div class="alert alert-warn">O Mercado Pago nao esta configurado neste servidor.</div><? } ?>

                <div class="label">Status atual</div>
                <? $status_class = 'status-pill'; $status_key = str_replace('-', '_', (string)$detail['subscription']->status); if(in_array($detail['subscription']->status, ['active','trial','past_due','canceled','paused'])){ $status_class .= ' status-'.$status_key; } ?>
                <div style="margin-top:10px;"><span class="<?=$status_class?>"><?=$detail['subscription']->status?></span></div>
                <p class="copy" style="margin-top:14px;">
                    Use "Atualizar status" depois de pagar para conferir rapidamente o retorno do Mercado Pago.
                </p>
                <div class="foot-actions">
                    <a class="btn btn-secondary" href="<?=$status_refresh_url?>">Atualizar status</a>
                    <a class="btn btn-secondary" href="<?=$back_url?>">Voltar</a>
                </div>
            </div>
        </div>

        <div class="method-grid">
            <div class="method-card">
                <h2>PIX</h2>
                <p class="method-copy">Gere um QR Code instantaneo para este ciclo. O usuario pode pagar pelo app do banco, copiar o codigo Pix ou abrir a tela hospedada do Mercado Pago.</p>

                <? if($mercadopago_ready && $open_cycle){ ?>
                    <div class="btn-row">
                        <form method="post" action="<?=$pix_submit_url?>" style="margin:0;">
                            <button class="btn btn-primary" type="submit">Gerar PIX agora</button>
                        </form>
                        <? if(isset($latest_payment_payload['point_of_interaction']['transaction_data']['ticket_url']) && trim((string)$latest_payment_payload['point_of_interaction']['transaction_data']['ticket_url']) !== ''){ ?>
                            <a class="btn btn-secondary" target="_blank" href="<?=$latest_payment_payload['point_of_interaction']['transaction_data']['ticket_url']?>">Abrir tela do Mercado Pago</a>
                        <? } ?>
                    </div>
                <? } ?>

                <? if(isset($latest_payment_payload['point_of_interaction']['transaction_data'])){ ?>
                    <? $transaction = $latest_payment_payload['point_of_interaction']['transaction_data']; ?>
                    <div class="pix-box">
                        <div class="label">Pagamento Pix gerado</div>
                        <p class="copy">Status Mercado Pago: <?=isset($latest_payment_payload['status']) ? $latest_payment_payload['status'] : 'pending'?><?=isset($latest_payment_payload['status_detail']) && trim((string)$latest_payment_payload['status_detail']) !== '' ? ' / '.$latest_payment_payload['status_detail'] : ''?></p>
                        <? if(isset($transaction['qr_code_base64']) && trim((string)$transaction['qr_code_base64']) !== ''){ ?>
                            <img class="pix-qr" src="data:image/jpeg;base64,<?=$transaction['qr_code_base64']?>" alt="QR Code Pix">
                        <? } ?>
                        <? if(isset($transaction['qr_code']) && trim((string)$transaction['qr_code']) !== ''){ ?>
                            <div class="label" style="margin-bottom:8px;">Pix copia e cola</div>
                            <textarea class="pix-code" readonly><?=$transaction['qr_code']?></textarea>
                        <? } ?>
                    </div>
                <? } ?>
            </div>

            <div class="method-card">
                <h2>Cartao de credito</h2>
                <p class="method-copy">O formulario abaixo usa o Brick oficial do Mercado Pago para tokenizar o cartao no frontend e enviar o pagamento ao backend com seguranca.</p>

                <? if(!$mercadopago_public_key){ ?>
                    <div class="alert alert-warn">A chave publica do Mercado Pago nao foi configurada. O pagamento por cartao depende dela.</div>
                <? elseif(!$open_cycle){ ?>
                    <div class="alert alert-ok">Nao existe ciclo pendente para cobrar com cartao.</div>
                <? else { ?>
                    <div id="card-brick" class="card-brick-wrap"></div>
                    <div id="card-loader" class="loader">Carregando formulario de cartao...</div>
                <? } ?>
            </div>
        </div>
    </div>

    <? if($mercadopago_public_key && $open_cycle){ ?>
        <script src="https://sdk.mercadopago.com/js/v2"></script>
        <script>
            (function () {
                var cardContainer = document.getElementById('card-brick');
                var loader = document.getElementById('card-loader');
                if (!cardContainer || !window.MercadoPago) {
                    if (loader) loader.textContent = 'Nao foi possivel carregar o formulario do Mercado Pago.';
                    return;
                }

                var mp = new MercadoPago('<?=$mercadopago_public_key?>');
                var bricksBuilder = mp.bricks();

                var payerIdentification = null;
                <? $payer_document_digits = preg_replace('/\D+/', '', (string)$detail['tenant']->documento); ?>
                <? if($payer_document_digits !== ''){ ?>
                payerIdentification = {
                    type: <?=json_encode(strlen($payer_document_digits) === 14 ? 'CNPJ' : 'CPF')?>,
                    number: <?=json_encode($payer_document_digits)?>
                };
                <? } ?>

                var renderCardPaymentBrick = async function () {
                    var settings = {
                        initialization: {
                            amount: <?=json_encode((float)$open_cycle->amount_due)?>,
                            payer: {
                                email: <?=json_encode((string)$detail['owner']->email)?>
                            }
                        },
                        customization: {
                            paymentMethods: {
                                types: {
                                    excluded: ['debit_card', 'prepaid_card']
                                }
                            }
                        },
                        callbacks: {
                            onReady: function () {
                                if (loader) loader.style.display = 'none';
                            },
                            onSubmit: function (formData) {
                                return new Promise(function (resolve, reject) {
                                    fetch(<?=json_encode($card_submit_url)?>, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify(formData)
                                    })
                                    .then(function (response) {
                                        return response.json().then(function (json) {
                                            if (!response.ok || !json.ok) {
                                                throw new Error(json.message || 'Falha ao processar pagamento.');
                                            }
                                            alert(json.message || 'Pagamento enviado com sucesso.');
                                            if (json.redirect_url) {
                                                window.location.href = json.redirect_url;
                                                return;
                                            }
                                            resolve();
                                        });
                                    })
                                    .catch(function (error) {
                                        alert(error.message || 'Falha ao processar pagamento.');
                                        reject(error);
                                    });
                                });
                            },
                            onError: function (error) {
                                console.error(error);
                                if (loader) {
                                    loader.style.display = 'block';
                                    loader.textContent = 'O formulario encontrou um erro. Revise os dados do cartao e tente novamente.';
                                }
                            }
                        }
                    };

                    if (payerIdentification) {
                        settings.initialization.payer.identification = payerIdentification;
                    }

                    window.cardPaymentBrickController = await bricksBuilder.create(
                        'cardPayment',
                        'card-brick',
                        settings
                    );
                };

                renderCardPaymentBrick();
            })();
        </script>
    <? } ?>
</body>
</html>
