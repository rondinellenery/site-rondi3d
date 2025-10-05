<?php
require __DIR__.'/config.php';
$title = 'Calculadora 3D';

$DEF       = CALC_DEFAULTS;
$isAdmin   = is_admin();
$forBudget = (int)($_GET['for_budget'] ?? 0);
$returnUrl = trim($_GET['return'] ?? '');
$csrf      = csrf_token();

include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Calculadora 3D</h1>

<?php if ($forBudget && $returnUrl): ?>
  <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
    <div class="me-3">Informe peso e tempo para registrar o cálculo deste orçamento.</div>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h($returnUrl) ?>">Cancelar cálculo</a>
  </div>
<?php endif; ?>

<p class="text-muted mb-4">
  Informe apenas <strong>peso (g)</strong> e <strong>tempo</strong>. Os demais custos usam os valores padrão.
</p>

<form id="calcForm" class="row g-3">
  <div class="col-sm-4">
    <label class="form-label">Peso do material (g) *</label>
    <input type="number" step="1" min="1" class="form-control" id="inWeight" required placeholder="ex.: 120">
    <div class="form-text">Peso em gramas (do slicer).</div>
  </div>
  <div class="col-sm-2">
    <label class="form-label">Tempo — horas *</label>
    <input type="number" step="1" min="0" class="form-control" id="inH" required placeholder="ex.: 2">
  </div>
  <div class="col-sm-2">
    <label class="form-label">Tempo — minutos *</label>
    <input type="number" step="1" min="0" max="59" class="form-control" id="inM" required placeholder="ex.: 30">
  </div>
  <div class="col-sm-4">
    <label class="form-label">Material (opcional)</label>
    <select id="inMaterial" class="form-select">
      <option>Indefinido</option>
      <option>PLA</option>
      <option>PETG</option>
      <option>Resina</option>
    </select>
  </div>

  <?php if ($isAdmin): ?>
  <div class="col-12"><hr></div>
  <div class="col-sm-3">
    <label class="form-label">Preço por kg</label>
    <input type="number" step="0.01" class="form-control" id="ad_price" value="<?=h($DEF['price_per_kg'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Energia (kWh)</label>
    <input type="number" step="0.01" class="form-control" id="ad_energy" value="<?=h($DEF['energy_cost'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Watts</label>
    <input type="number" step="1" class="form-control" id="ad_watts" value="<?=h($DEF['power_watts'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Falhas (%)</label>
    <input type="number" step="0.01" class="form-control" id="ad_fail" value="<?=h($DEF['failure_rate'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Custo da impressora</label>
    <input type="number" step="0.01" class="form-control" id="ad_prn" value="<?=h($DEF['printer_cost'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Uso diário (h)</label>
    <input type="number" step="0.1" class="form-control" id="ad_daily" value="<?=h($DEF['daily_usage_h'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Vida útil (meses)</label>
    <input type="number" step="1" class="form-control" id="ad_life" value="<?=h($DEF['printer_life_m'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Manutenção</label>
    <input type="number" step="0.01" class="form-control" id="ad_maint" value="<?=h($DEF['maintenance'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Frete</label>
    <input type="number" step="0.01" class="form-control" id="ad_freight" value="<?=h($DEF['freight'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Mão de obra (modo)</label>
    <select id="ad_labor_mode" class="form-select">
      <option value="percent" <?= $DEF['labor_mode']==='percent'?'selected':''?>>Percentual</option>
      <option value="hourly"  <?= $DEF['labor_mode']==='hourly'?'selected':''?>>Por hora</option>
    </select>
  </div>
  <div class="col-sm-3">
    <label class="form-label">% mão de obra</label>
    <input type="number" step="0.01" class="form-control" id="ad_labor_pct" value="<?=h($DEF['labor_percent'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">R$/h mão de obra</label>
    <input type="number" step="0.01" class="form-control" id="ad_labor_rate" value="<?=h($DEF['labor_rate'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Lucro (%)</label>
    <input type="number" step="0.01" class="form-control" id="ad_profit" value="<?=h($DEF['profit_percent'])?>">
  </div>
  <div class="col-sm-3">
    <label class="form-label">Impostos (%)</label>
    <input type="number" step="0.01" class="form-control" id="ad_tax" value="<?=h($DEF['tax_percent'])?>">
  </div>
  <?php endif; ?>

  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Calcular</button>
    <button class="btn btn-outline-secondary" type="button" id="btClear">Limpar</button>

    <?php if ($isAdmin): ?>
      <button class="btn btn-success ms-auto" type="button" id="btSaveBudget">
        Salvar no orçamento
      </button>
    <?php endif; ?>

    <?php if ($forBudget && $returnUrl): ?>
      <a class="btn btn-outline-secondary ms-auto" href="<?= h($returnUrl) ?>">Cancelar cálculo</a>
    <?php endif; ?>
  </div>
</form>

<hr class="my-4">

<div id="resultWrap" class="row g-3" style="display:none;">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h2 class="h5">Preço final estimado</h2>
        <p class="display-6 mb-1" id="outFinal">—</p>
        <div class="text-muted small">
          Tempo total: <span id="outTime">—</span> · Peso: <span id="outWeight">—</span> g
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8" id="adminBreakCol" style="<?= $isAdmin?'':'display:none;' ?>">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="h6">Detalhamento (admin)</h3>
        <ul class="list-unstyled mb-0" id="outBreak"></ul>
      </div>
    </div>
  </div>
</div>

<!-- Modal: “Quer criar orçamento?” -->
<div class="modal fade" id="askQuoteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Criar orçamento?</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
    </div>
    <div class="modal-body">
      Vimos que você fez um cálculo sem orçamento. Quer nos contar mais detalhes do seu projeto?
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
      <a id="goNewQuote" href="<?= h(BASE_URL) ?>novo-orcamento.php?usecalc=1" class="btn btn-primary">Criar orçamento</a>
    </div>
  </div></div>
</div>

<script>
const IS_ADMIN   = <?= $isAdmin ? 'true' : 'false' ?>;
const FOR_BUDGET = <?= (int)$forBudget ?>;
const RETURN_URL = <?= json_encode($returnUrl) ?>;
const CSRF       = <?= json_encode($csrf) ?>;

window.saveCalcResult = (payload)=> sessionStorage.setItem('calc_result', JSON.stringify(payload));
window.clearCalcResult= ()=> sessionStorage.removeItem('calc_result');

function brl(n){ return 'R$ '+(n||0).toFixed(2).replace('.',','); }
function fmtHM(hoursDec){
  const h = Math.floor(hoursDec);
  let m = Math.round((hoursDec - h) * 60);
  if (m === 60){ m = 0; return `${h+1}h`; }
  if (h > 0 && m > 0) return `${h}h ${m}min`;
  if (h > 0 && m === 0) return `${h}h`;
  return `${m}min`;
}
function getAdminParams(){
  if (!IS_ADMIN){
    return <?= json_encode($DEF, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  }
  return {
    price_per_kg:  parseFloat(document.getElementById('ad_price').value||'0'),
    energy_cost:   parseFloat(document.getElementById('ad_energy').value||'0'),
    power_watts:   parseFloat(document.getElementById('ad_watts').value||'0'),
    failure_rate:  parseFloat(document.getElementById('ad_fail').value||'0'),
    printer_cost:  parseFloat(document.getElementById('ad_prn').value||'0'),
    daily_usage_h: parseFloat(document.getElementById('ad_daily').value||'0'),
    printer_life_m:parseFloat(document.getElementById('ad_life').value||'0'),
    maintenance:   parseFloat(document.getElementById('ad_maint').value||'0'),
    freight:       parseFloat(document.getElementById('ad_freight').value||'0'),
    labor_mode:    document.getElementById('ad_labor_mode').value,
    labor_percent: parseFloat(document.getElementById('ad_labor_pct').value||'0'),
    labor_rate:    parseFloat(document.getElementById('ad_labor_rate').value||'0'),
    profit_percent:parseFloat(document.getElementById('ad_profit').value||'0'),
    tax_percent:   parseFloat(document.getElementById('ad_tax').value||'0'),
  };
}
function calcCost(weightG,timeH,material, P){
  const priceKg   = P.price_per_kg;
  const energyKwh = P.energy_cost;
  const watts     = P.power_watts;
  const failPct   = P.failure_rate;
  const prnCost   = P.printer_cost;
  const dailyH    = P.daily_usage_h;
  const lifeM     = P.printer_life_m;
  const maint     = P.maintenance;
  const freight   = P.freight;
  const laborMode = P.labor_mode;
  const laborPct  = P.labor_percent;
  const laborRate = P.labor_rate;
  const profitPct = P.profit_percent;
  const taxPct    = P.tax_percent;

  const costMaterial = (weightG/1000) * priceKg;
  const energy = (watts/1000) * timeH * energyKwh;

  const monthlyHours   = dailyH * 30;
  const totalHoursLife = monthlyHours * lifeM;
  const depHour = totalHoursLife>0 ? (prnCost/totalHoursLife) : 0;
  const depTot  = depHour * timeH;

  const base = (costMaterial + energy + depTot + maint) * (1 + (failPct/100));

  let laborValue = 0, withLabor = base;
  if (laborMode==='hourly' && laborRate>0){
    laborValue = laborRate * timeH;
    withLabor  = base + laborValue;
  } else if (laborPct>0 && laborPct<100){
    withLabor  = base / (1 - (laborPct/100));
    laborValue = withLabor - base;
  }

  const withProfit = withLabor / (1 - (profitPct/100));
  const taxValue   = (taxPct>0 && taxPct<100) ? withProfit*(taxPct/100) : 0;
  const finalPrice = withProfit + taxValue + freight;

  return {peso:weightG,t:timeH,material,
    costMaterial, energy, depHour, depTot, base,
    laborValue, withLabor, withProfit, taxValue, freight, finalPrice,
    params:P
  };
}

document.getElementById('calcForm').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const g = parseFloat(document.getElementById('inWeight').value||'0');
  const h = parseFloat(document.getElementById('inH').value||'0');
  const m = parseFloat(document.getElementById('inM').value||'0');
  if (g<=0 || (h<=0 && m<=0)){ alert('Informe peso e tempo válidos.'); return; }
  const t = h + (m/60);
  const material = document.getElementById('inMaterial').value;

  const P = getAdminParams();
  const r = calcCost(g,t,material,P);

  // UI
  document.getElementById('resultWrap').style.display='';
  document.getElementById('outFinal').textContent  = brl(r.finalPrice);
  document.getElementById('outTime').textContent   = fmtHM(t);
  document.getElementById('outWeight').textContent = g.toFixed(0);
  if (IS_ADMIN){
    const b = document.getElementById('outBreak');
    b.innerHTML = `
      <li>Material: <strong>${brl(r.costMaterial)}</strong></li>
      <li>Energia: <strong>${brl(r.energy)}</strong></li>
      <li>Depreciação total: <strong>${brl(r.depTot)}</strong> (≈ ${brl(r.depHour)}/h)</li>
      <li>Base c/ falhas: <strong>${brl(r.base)}</strong></li>
      <li>Mão de obra: <strong>${brl(r.laborValue)}</strong></li>
      <li>C/ mão de obra: <strong>${brl(r.withLabor)}</strong></li>
      <li>Lucro aplicado: <strong>${brl(r.withProfit - r.withLabor)}</strong></li>
      <li>Impostos: <strong>${brl(r.taxValue)}</strong></li>
      <li>Frete: <strong>${brl(r.freight)}</strong></li>
    `;
  }

  // guarda no sessionStorage (inclui params e timestamp)
  window.saveCalcResult({
    peso: r.peso, t: r.t, finalPrice: r.finalPrice,
    material,
    breakdown: {
      costMaterial:r.costMaterial, energy:r.energy, depTot:r.depTot,
      base:r.base, laborValue:r.laborValue, withLabor:r.withLabor,
      withProfit:r.withProfit, taxValue:r.taxValue, freight:r.freight
    },
    params: r.params,
    ts: Date.now()
  });

  // Se é para um orçamento específico, salva no banco e retorna
  if (FOR_BUDGET && RETURN_URL){
    try{
      const resp = await fetch('<?= h(BASE_URL) ?>calculo-salvar.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
          csrf: CSRF,
          id: FOR_BUDGET,
          weight_g: r.peso,
          time_hours: r.t,
          total_price: r.finalPrice,
          material,
          breakdown: {
            ...r.params,
            costMaterial:r.costMaterial, energy:r.energy, depTot:r.depTot,
            base:r.base, laborValue:r.laborValue, withLabor:r.withLabor,
            withProfit:r.withProfit, taxValue:r.taxValue, freight:r.freight
          }
        })
      }).then(x=>x.json());

      if (resp && resp.ok){
        window.location = RETURN_URL;
      } else {
        alert(resp?.error || 'Falha ao salvar no orçamento.');
      }
    }catch(e){
      alert('Falha de comunicação ao salvar no orçamento.');
    }
    return;
  }

  // Caso não esteja atrelado a um orçamento: oferece criar um
  const modal = new bootstrap.Modal('#askQuoteModal');
  modal.show();
});

// limpar
document.getElementById('btClear').addEventListener('click', ()=>{
  document.getElementById('calcForm').reset();
  document.getElementById('resultWrap').style.display='none';
  window.clearCalcResult();
});

// admin: salvar no orçamento atual ou escolhido
const btSave = document.getElementById('btSaveBudget');
if (btSave){
  btSave.addEventListener('click', async ()=>{
    let id = FOR_BUDGET || parseInt(prompt('Salvar em qual #ID de orçamento?')||'0',10);
    if (!id) return;

    const r = JSON.parse(sessionStorage.getItem('calc_result')||'null');
    if (!r){ alert('Faça um cálculo antes de salvar.'); return; }

    try{
      const resp = await fetch('<?= h(BASE_URL) ?>calculo-salvar.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
          csrf: CSRF,
          id,
          weight_g: r.peso,
          time_hours: r.t,
          total_price: r.finalPrice,
          material: r.material || 'Indefinido',
          breakdown: {
            ...(r.params || {}),
            ...(r.breakdown || {})
          }
        })
      }).then(x=>x.json());

      if (resp && resp.ok){
        window.location = '<?= h(BASE_URL) ?>meus-orcamentos-view.php?id='+id;
      } else {
        alert(resp?.error || 'Não foi possível salvar.');
      }
    }catch(e){
      alert('Erro ao salvar no orçamento.');
    }
  });
}
</script>

<?php include __DIR__.'/includes/footer.php';
