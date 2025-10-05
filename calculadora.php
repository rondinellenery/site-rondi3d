<?php
require __DIR__.'/config.php';
$title = "Calculadora 3D";
$C = CALC_DEFAULTS;
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Calculadora de Custos de Impressão 3D</h1>
<p class="text-muted">Informe o <strong>peso</strong> (g) e o <strong>tempo</strong>. Os demais custos são internos da Rondi3D.</p>

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Peso do material (g)</label>
    <input id="peso" type="number" class="form-control" min="0" step="0.01" placeholder="ex.: 120" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Tempo (horas)</label>
    <input id="horas" type="number" class="form-control" min="0" step="0.01" placeholder="ex.: 2">
  </div>
  <div class="col-md-4">
    <label class="form-label">Tempo (minutos)</label>
    <input id="minutos" type="number" class="form-control" min="0" step="1" placeholder="ex.: 30">
  </div>

  <div class="col-12">
    <button id="btnCalc" class="btn btn-primary">Calcular</button>
    <span id="result" class="ms-3 fw-bold"></span>
  </div>

  <div class="col-12">
    <button id="btnToQuote" class="btn btn-success" disabled>Gerar orçamento</button>
  </div>
</div>

<script>
const FIXO = <?= json_encode($C) ?>;
function brl(v){ return v.toLocaleString('pt-BR',{style:'currency',currency:'BRL'}); }

function calc(){
  const peso = parseFloat(document.getElementById('peso').value||0);
  const h = parseFloat(document.getElementById('horas').value||0);
  const m = parseFloat(document.getElementById('minutos').value||0);
  const t = h + (m/60.0);

  const costMaterial = (peso/1000.0) * FIXO.price_per_kg;
  const energy = (FIXO.power_watts/1000.0) * t * FIXO.energy_cost;
  const monthlyHours = FIXO.daily_usage_h * 30.0;
  const totalLifeH = monthlyHours * FIXO.printer_life_m;
  const depH = totalLifeH>0 ? (FIXO.printer_cost/totalLifeH) : 0;
  const dep = depH * t;

  const base = (costMaterial + energy + dep + FIXO.maintenance) * (1 + (FIXO.failure_rate/100.0));

  let costWithLabor = base, laborValue = 0;
  if (FIXO.labor_mode === 'hourly' && FIXO.labor_rate>0){
    laborValue = FIXO.labor_rate * t;
    costWithLabor = base + laborValue;
  } else if (FIXO.labor_percent>0 && FIXO.labor_percent<100){
    costWithLabor = base / (1 - (FIXO.labor_percent/100.0));
    laborValue = costWithLabor - base;
  }

  let price = costWithLabor / (1 - (FIXO.profit_percent/100.0));
  const tax = FIXO.tax_percent>0 ? price * (FIXO.tax_percent/100.0) : 0;
  const finalPrice = price + tax + FIXO.freight;

  return { peso, t, costMaterial, energy, depH, dep, base, laborValue, costWithLabor, price, tax, finalPrice };
}

document.getElementById('btnCalc').addEventListener('click', () => {
  const r = calc();
  document.getElementById('result').textContent = "Preço estimado: " + brl(r.finalPrice);
  document.getElementById('btnToQuote').disabled = (r.finalPrice<=0);
  sessionStorage.setItem('calc_result', JSON.stringify(r));
});
document.getElementById('btnToQuote').addEventListener('click', () => {
  window.location.href = 'novo-orcamento.php';
});
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
