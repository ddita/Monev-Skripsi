<!-- jQuery -->
<script src="../assets_adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets_adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- overlayScrollbars -->
<script src="../assets_adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- AdminLTE App -->
<script src="../assets_adminlte/dist/js/adminlte.min.js"></script>

<!-- DataTables (jika perlu) -->
<script src="../assets_adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets_adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets_adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets_adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- ================= DATATABLE ================= -->
<script>
  $(function() {
    $('#example1').DataTable({
      responsive: true,
      autoWidth: false,
      lengthChange: true,
      searching: true,
      ordering: true,
      paging: true,
      info: true,
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          previous: "‹",
          next: "›"
        }
      }
    });
  });
</script>

<!-- ================= CHART JS (FIXED) ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function getChartTextColor() {
    return document.body.classList.contains('dark-mode') ?
      '#f8fafc' :
      '#1f2937';
  }

  function getGridColor() {
    return document.body.classList.contains('dark-mode') ?
      'rgba(255,255,255,0.1)' :
      'rgba(0,0,0,0.1)';
  }

  function getTooltipStyle() {
    if (document.body.classList.contains('dark-mode')) {
      return {
        bg: '#0f172a',
        text: '#f8fafc',
        border: 'rgba(255,255,255,0.15)'
      };
    }
    return {
      bg: '#ffffff',
      text: '#1f2937',
      border: 'rgba(0,0,0,0.15)'
    };
  }
</script>

<!-- ================= TEMA, CHART, DAN PRELOADER (GABUNGAN LENGKAP) ================= -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    // ================= INISIALISASI TEMA =================
    const theme = localStorage.getItem("theme") || "dark";
    document.body.classList.add(theme + "-mode");
    document.documentElement.classList.add(theme + "-mode");

    // ================= TOGGLE TEMA =================
    const toggle = document.getElementById("themeToggle");
    if (toggle) {
      const icon = toggle.querySelector("i");
      icon.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon";

      toggle.addEventListener("click", function(e) {
        e.preventDefault();

        if (document.body.classList.contains("dark-mode")) {
          document.body.classList.replace("dark-mode", "light-mode");
          document.documentElement.classList.replace("dark-mode", "light-mode");
          localStorage.setItem("theme", "light");
          icon.className = "fas fa-moon";
        } else {
          document.body.classList.replace("light-mode", "dark-mode");
          document.documentElement.classList.replace("light-mode", "dark-mode");
          localStorage.setItem("theme", "dark");
          icon.className = "fas fa-sun";
        }

        // Update chart jika ada (akan dijalankan setelah toggle)
        updateChartOnThemeChange();
      });
    }

    // ================= CHART JS =================
    const ctx = document.getElementById('grafikProgres');
    let chart = null; // Variabel global untuk chart agar bisa diupdate
    if (ctx) {
      const tooltip = getTooltipStyle();

      chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($labelProgres ?? []); ?>, // Pastikan variabel PHP ada, default kosong jika tidak
          datasets: [{
            data: <?= json_encode($dataProgres ?? []); ?>, // Pastikan variabel PHP ada, default kosong jika tidak
            backgroundColor: ['#0d6efd', '#ffc107', '#dc3545', '#198754', '#6c757d'],
            borderRadius: 8,
            barThickness: 40
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: tooltip.bg,
              titleColor: tooltip.text,
              bodyColor: tooltip.text,
              borderColor: tooltip.border,
              borderWidth: 1,
              callbacks: {
                label: ctx => ctx.raw + ' mahasiswa'
              }
            }
          },
          scales: {
            x: {
              ticks: {
                color: getChartTextColor()
              },
              grid: {
                color: getGridColor()
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                color: getChartTextColor(),
                precision: 0
              },
              title: {
                display: true,
                text: 'Jumlah Mahasiswa',
                color: getChartTextColor()
              },
              grid: {
                color: getGridColor()
              }
            }
          }
        }
      });
    }

    // Fungsi untuk update chart saat tema berubah
    function updateChartOnThemeChange() {
      if (chart) {
        setTimeout(() => {
          const t = getTooltipStyle();

          chart.options.scales.x.ticks.color = getChartTextColor();
          chart.options.scales.y.ticks.color = getChartTextColor();
          chart.options.scales.y.title.color = getChartTextColor();
          chart.options.scales.x.grid.color = getGridColor();
          chart.options.scales.y.grid.color = getGridColor();

          chart.options.plugins.tooltip.backgroundColor = t.bg;
          chart.options.plugins.tooltip.titleColor = t.text;
          chart.options.plugins.tooltip.bodyColor = t.text;
          chart.options.plugins.tooltip.borderColor = t.border;

          chart.update();
        }, 100);
      }
    }

    // ================= SEMBUNYIKAN PRELOADER =================
    const preloader = document.querySelector(".preloader");
    if (preloader) {
      preloader.style.display = "none"; // Atau gunakan $(preloader).fadeOut(500); jika ingin animasi
    }
  });
</script>

<!-- ================= FORM SUBMIT (UNTUK HALAMAN LAIN YANG MEMILIKI FORM) ================= -->
<script>
  document.querySelector(".form-mahasiswa")?.addEventListener("submit", function() {
    document.querySelector(".preloader").style.display = "none";
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>