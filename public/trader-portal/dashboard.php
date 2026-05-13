<?php

declare(strict_types=1);

$pageTitle = 'Trader dashboard — GroceryGo';
$mainId = 'traderDashboard';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Trader dashboard</h1>
  <hr class="wf-page-rule">

  <section class="kpi-row" aria-label="Key metrics">
    <article class="kpi-card">
      <p class="kpi-label">Total orders today</p>
      <p class="kpi-value">24</p>
    </article>
    <article class="kpi-card">
      <p class="kpi-label">Pending orders</p>
      <p class="kpi-value">2</p>
    </article>
    <article class="kpi-card">
      <p class="kpi-label">Low stock alerts</p>
      <p class="kpi-value">6</p>
    </article>
    <article class="kpi-card">
      <p class="kpi-label">Weekly earnings</p>
      <p class="kpi-value">Rs 500,000</p>
    </article>
  </section>

  <section class="slot-board" aria-labelledby="slot-heading">
    <h2 id="slot-heading" class="wf-section-title">Orders by collection slot</h2>
    <div class="slot-board-grid">
      <div>
        <h3 class="slot-day-title">Wednesday</h3>
        <div class="slot-card">
          <time>10AM – 1PM</time>
          <p class="slot-meta">4 orders</p>
          <strong class="slot-meta">Order ID: #1001, #1002, #1004</strong>
          <p class="slot-meta"><strong>20 products</strong></p>
        </div>
        <div class="slot-card">
          <time>1PM – 4PM</time>
          <p class="slot-meta">3 orders</p>
          <strong class="slot-meta">Order ID: #1008, #1009</strong>
          <p class="slot-meta"><strong>14 products</strong></p>
        </div>
        <div class="slot-card">
          <time>4PM – 6PM</time>
          <p class="slot-meta">2 orders</p>
          <strong class="slot-meta">Order ID: #1012</strong>
          <p class="slot-meta"><strong>9 products</strong></p>
        </div>
      </div>
      <div>
        <h3 class="slot-day-title">Thursday</h3>
        <div class="slot-card">
          <time>10AM – 1PM</time>
          <p class="slot-meta">5 orders</p>
          <strong class="slot-meta">Order ID: #1015–#1019</strong>
          <p class="slot-meta"><strong>28 products</strong></p>
        </div>
        <div class="slot-card">
          <time>1PM – 4PM</time>
          <p class="slot-meta">2 orders</p>
          <strong class="slot-meta">Order ID: #1020, #1021</strong>
          <p class="slot-meta"><strong>11 products</strong></p>
        </div>
        <div class="slot-card">
          <time>4PM – 6PM</time>
          <p class="slot-meta">6 orders</p>
          <strong class="slot-meta">Order ID: #1024–#1029</strong>
          <p class="slot-meta"><strong>33 products</strong></p>
        </div>
      </div>
      <div>
        <h3 class="slot-day-title">Friday</h3>
        <div class="slot-card">
          <time>10AM – 1PM</time>
          <p class="slot-meta">7 orders</p>
          <strong class="slot-meta">Order ID: #1030–#1036</strong>
          <p class="slot-meta"><strong>41 products</strong></p>
        </div>
        <div class="slot-card">
          <time>1PM – 4PM</time>
          <p class="slot-meta">4 orders</p>
          <strong class="slot-meta">Order ID: #1038–#1041</strong>
          <p class="slot-meta"><strong>18 products</strong></p>
        </div>
        <div class="slot-card">
          <time>4PM – 6PM</time>
          <p class="slot-meta">3 orders</p>
          <strong class="slot-meta">Order ID: #1042–#1044</strong>
          <p class="slot-meta"><strong>15 products</strong></p>
        </div>
      </div>
    </div>
  </section>

  <section class="chart-panel-wf">
    <h2 class="wf-section-title">Weekly sales trend</h2>
    <div class="chart-wrap"><canvas id="chartWeeklySales" aria-label="Weekly sales chart"></canvas></div>
  </section>

  <section aria-labelledby="quick-heading">
    <h2 id="quick-heading" class="wf-section-title">Quick actions</h2>
    <div class="quick-actions">
      <a class="quick-action-btn" href="add-product.php">Add product</a>
      <a class="quick-action-btn" href="update-product.php">Update product</a>
      <a class="quick-action-btn" href="profile.php">Update shop info</a>
      <a class="quick-action-btn" href="reports.php">View report</a>
    </div>
  </section>

  <p class="muted small"><a href="index.php">Preview hub</a></p>
<?php
$chartJs = true;
$inlineScript = <<<'HTML'
<script>
(function () {
  var el = document.getElementById("chartWeeklySales");
  if (!el || typeof Chart === "undefined") return;
  new Chart(el, {
    type: "line",
    data: {
      labels: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      datasets: [{
        label: "Sales (Rs)",
        data: [0, 8200, 6400, 5800, 7200, 9100, 4800],
        borderColor: "#1f7a4d",
        backgroundColor: "rgba(31, 122, 77, 0.12)",
        fill: true,
        tension: 0.35,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          max: 10000,
          ticks: { stepSize: 2000 },
          grid: { color: "rgba(0,0,0,0.06)" },
        },
        x: { grid: { display: false } },
      },
    },
  });
})();
</script>
HTML;

require __DIR__ . '/includes/layout-app-end.php';
