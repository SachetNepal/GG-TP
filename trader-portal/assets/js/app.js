/**
 * Trader portal: mobile sidebar, Chart.js dashboard, product form AJAX, toasts.
 */
(function () {
  "use strict";

  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }

  function qsa(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  /* --- Mobile sidebar --- */
  var toggle = qs("#sidebarToggle");
  var sidebar = qs("#traderSidebar");
  var backdrop = qs("#sidebarBackdrop");
  if (toggle && sidebar) {
    function openSb() {
      sidebar.classList.add("open");
      if (backdrop) {
        backdrop.hidden = false;
      }
    }
    function closeSb() {
      sidebar.classList.remove("open");
      if (backdrop) {
        backdrop.hidden = true;
      }
    }
    toggle.addEventListener("click", function () {
      if (sidebar.classList.contains("open")) {
        closeSb();
      } else {
        openSb();
      }
    });
    if (backdrop) {
      backdrop.addEventListener("click", closeSb);
    }
  }

  /* --- Toast --- */
  window.showToast = function (message, type) {
    var root = qs("#toast-root");
    if (!root) {
      return;
    }
    var el = document.createElement("div");
    el.className = "toast";
    el.textContent = message;
    if (type === "error") {
      el.style.background = "#991b1b";
    }
    root.appendChild(el);
    setTimeout(function () {
      el.remove();
    }, 4200);
  };

  /* --- Dashboard Chart.js --- */
  function initDashboardChart() {
    var data = window.__DASHBOARD_CHART__;
    var canvas = qs("#chartRevenue");
    if (!data || !canvas || typeof Chart === "undefined") {
      return;
    }
    var labels = data.map(function (d) {
      return d.day;
    });
    var amounts = data.map(function (d) {
      return d.amount;
    });
    new Chart(canvas.getContext("2d"), {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Revenue (USD $)",
            data: amounts,
            backgroundColor: "rgba(31, 122, 77, 0.72)",
            borderColor: "#1F7A4D",
            borderWidth: 1,
            borderRadius: 8,
            borderSkipped: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: "#6B7280", font: { size: 12 } },
          },
          y: {
            beginAtZero: true,
            grid: { color: "rgba(229, 231, 235, 0.8)" },
            ticks: {
              color: "#6B7280",
              font: { size: 12 },
              callback: function (v) {
                return "$" + v;
              },
            },
          },
        },
      },
    });
  }

  /* --- Refresh stats via AJAX (optional) --- */
  function refreshStats() {
    var url = window.__API_STATS__;
    if (!url) {
      return;
    }
    fetch(url, { credentials: "same-origin" })
      .then(function (r) {
        return r.json();
      })
      .then(function (j) {
        if (!j.ok) {
          return;
        }
        var elR = qs("#statRevenue");
        var elO = qs("#statOrders");
        var elP = qs("#statProducts");
        var elS = qs("#statSlots");
        if (elR) {
          elR.textContent =
            "$" +
            Number(j.revenue || 0).toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            });
        }
        if (elO) {
          elO.textContent = String(j.orders || 0);
        }
        if (elP) {
          elP.textContent = String(j.products || 0);
        }
        if (elS) {
          elS.textContent = String(j.slots || 0);
        }
      })
      .catch(function () {});
  }

  /* --- Product form (add / edit) --- */
  function initProductForm() {
    var form = qs("#productForm");
    if (!form) {
      return;
    }

    var pills = qsa(".pill-btn");
    var tagsInput = qs("#tagsField");
    pills.forEach(function (btn) {
      btn.addEventListener("click", function () {
        btn.classList.toggle("selected");
        syncTags();
      });
    });

    function syncTags() {
      var sel = pills
        .filter(function (b) {
          return b.classList.contains("selected");
        })
        .map(function (b) {
          return b.getAttribute("data-tag");
        })
        .join(",");
      if (tagsInput) {
        tagsInput.value = sel;
      }
    }

    var dz = qs("#dropzone");
    var fileInput = qs("#imagesInput");
    var preview = qs("#imagePreview");

    if (dz && fileInput) {
      dz.addEventListener("click", function () {
        fileInput.click();
      });
      dz.addEventListener("dragover", function (e) {
        e.preventDefault();
        dz.classList.add("dragover");
      });
      dz.addEventListener("dragleave", function () {
        dz.classList.remove("dragover");
      });
      dz.addEventListener("drop", function (e) {
        e.preventDefault();
        dz.classList.remove("dragover");
        fileInput.files = e.dataTransfer.files;
        renderPreview(fileInput.files);
      });
      fileInput.addEventListener("change", function () {
        renderPreview(fileInput.files);
      });
    }

    function renderPreview(files) {
      if (!preview) {
        return;
      }
      preview.innerHTML = "";
      if (!files || !files.length) {
        return;
      }
      Array.prototype.forEach.call(files, function (f, idx) {
        if (!f.type.match(/^image\//)) {
          return;
        }
        var fig = document.createElement("figure");
        var img = document.createElement("img");
        img.alt = "";
        img.src = URL.createObjectURL(f);
        var rm = document.createElement("button");
        rm.type = "button";
        rm.className = "remove-img";
        rm.setAttribute("aria-label", "Remove");
        rm.textContent = "×";
        rm.addEventListener("click", function () {
          URL.revokeObjectURL(img.src);
          fig.remove();
        });
        fig.appendChild(img);
        fig.appendChild(rm);
        preview.appendChild(fig);
      });
    }

    function sendProduct(statusVal, after) {
      var st = qs("#statusField");
      if (st) {
        st.value = statusVal;
      }
      syncTags();
      var fd = new FormData(form);
      var action = form.getAttribute("action") || "";
      var csrfMeta = qs('meta[name="csrf-token"]');
      var csrf = csrfMeta ? csrfMeta.getAttribute("content") : "";
      fetch(action, {
        method: "POST",
        body: fd,
        credentials: "same-origin",
        headers: csrf ? { "X-CSRF-TOKEN": csrf } : {},
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (j) {
          if (j.ok) {
            showToast("Product saved.", "ok");
            if (typeof after === "function") {
              after();
            } else if (form.dataset.redirectPublished) {
              window.location.href = form.dataset.redirectPublished;
            }
          } else {
            showToast(j.error || "Save failed", "error");
          }
        })
        .catch(function () {
          showToast("Network error", "error");
        });
    }

    qsa("[data-product-action]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var act = btn.getAttribute("data-product-action");
        if (act === "publish") {
          sendProduct("published", function () {
            if (form.dataset.redirectPublished) {
              window.location.href = form.dataset.redirectPublished;
            }
          });
        } else if (act === "draft") {
          sendProduct("draft", null);
        } else if (act === "new") {
          sendProduct(
            qs("#statusField") ? qs("#statusField").value || "published" : "published",
            function () {
              form.reset();
              if (preview) {
                preview.innerHTML = "";
              }
              pills.forEach(function (b) {
                b.classList.remove("selected");
              });
              syncTags();
            }
          );
        }
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      sendProduct(
        (qs("#statusField") && qs("#statusField").value) || "published",
        null
      );
    });
  }

  function initToggleProducts() {
    qsa(".btn-toggle-product").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var url = btn.getAttribute("data-toggle-url");
        var pid = btn.getAttribute("data-product-id");
        var action = btn.getAttribute("data-action");
        var csrfMeta = qs('meta[name="csrf-token"]');
        var csrf = csrfMeta ? csrfMeta.getAttribute("content") : "";
        var fd = new FormData();
        fd.append("product_id", pid);
        fd.append("action", action);
        fd.append("_csrf", csrf);
        fetch(url, {
          method: "POST",
          body: fd,
          credentials: "same-origin",
          headers: csrf ? { "X-CSRF-TOKEN": csrf } : {},
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (j) {
            if (j.ok) {
              showToast("Product updated.");
              window.location.reload();
            } else {
              showToast(j.error || "Update failed", "error");
            }
          })
          .catch(function () {
            showToast("Network error", "error");
          });
      });
    });
  }

  function initOrderStatusForm() {
    var form = qs("#orderStatusForm");
    if (!form) {
      return;
    }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var csrfMeta = qs('meta[name="csrf-token"]');
      var csrf = csrfMeta ? csrfMeta.getAttribute("content") : "";
      var fd = new FormData(form);
      var url = form.getAttribute("data-update-url") || "";
      if (!url) {
        return;
      }
      fetch(url, {
        method: "POST",
        body: fd,
        credentials: "same-origin",
        headers: csrf ? { "X-CSRF-TOKEN": csrf } : {},
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (j) {
          if (j.ok) {
            showToast("Order status updated.");
            window.location.reload();
          } else {
            showToast(j.error || "Update failed", "error");
          }
        })
        .catch(function () {
          showToast("Network error", "error");
        });
    });
  }

  function initDeleteButtons() {
    qsa(".btn-delete-product").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (!confirm("Delete this product?")) {
          return;
        }
        var url = btn.getAttribute("data-delete-url");
        var pid = btn.getAttribute("data-product-id");
        var csrfMeta = qs('meta[name="csrf-token"]');
        var csrf = csrfMeta ? csrfMeta.getAttribute("content") : "";
        var fd = new FormData();
        fd.append("product_id", pid);
        fd.append("_csrf", csrf);
        fetch(url, {
          method: "POST",
          body: fd,
          credentials: "same-origin",
          headers: csrf ? { "X-CSRF-TOKEN": csrf } : {},
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (j) {
            if (j.ok) {
              showToast("Product deleted.");
              window.location.reload();
            } else {
              showToast(j.error || "Delete failed", "error");
            }
          })
          .catch(function () {
            showToast("Network error", "error");
          });
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initDashboardChart();
    refreshStats();
    initProductForm();
    initDeleteButtons();
    initToggleProducts();
    initOrderStatusForm();
  });
})();
