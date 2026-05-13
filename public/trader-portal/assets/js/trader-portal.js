/**
 * Trader portal static demo — sidebar + active nav.
 */
(function () {
  var sidebar = document.getElementById("traderSidebar");
  var toggle = document.getElementById("sidebarToggle");
  var backdrop = document.getElementById("sidebarBackdrop");

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove("open");
    if (backdrop) backdrop.hidden = true;
  }

  function openSidebar() {
    if (sidebar) sidebar.classList.add("open");
    if (backdrop) backdrop.hidden = false;
  }

  if (toggle && sidebar) {
    toggle.addEventListener("click", function () {
      if (sidebar.classList.contains("open")) closeSidebar();
      else openSidebar();
    });
  }

  if (backdrop) backdrop.addEventListener("click", closeSidebar);

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeSidebar();
  });

  var page = document.body.getAttribute("data-page") || "";
  var navMap = {
    "product-edit": "products",
    "order-detail": "orders",
  };
  var navKey = navMap[page] || page;

  document.querySelectorAll(".trader-sidebar-nav a[data-nav]").forEach(function (a) {
    if (a.getAttribute("data-nav") === navKey) {
      a.classList.add("active");
    }
  });
})();
