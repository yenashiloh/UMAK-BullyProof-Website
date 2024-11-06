   <!--   Core JS Files   -->
   <script src="../../../../assets/js/core/jquery-3.7.1.min.js"></script>
   <script src="../../../../assets/js/core/popper.min.js"></script>
   <script src="../../../../assets/js/core/bootstrap.min.js"></script>

   <!-- jQuery Scrollbar -->
   <script src="../../../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

   <!-- Chart JS -->
   <script src="../../../../assets/js/plugin/chart.js/chart.min.js"></script>

   <!-- jQuery Sparkline -->
   <script src="../../../../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

   <!-- Chart Circle -->
   <script src="../../../../assets/js/plugin/chart-circle/circles.min.js"></script>

   <!-- Datatables -->
   <script src="../../../../assets/js/plugin/datatables/datatables.min.js"></script>

   <!-- Bootstrap Notify -->
   <script src="../../../../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

   <!-- jQuery Vector Maps -->
   <script src="../../../../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
   <script src="../../../../assets/js/plugin/jsvectormap/world.js"></script>

   <!-- Sweet Alert -->
   <script src="../../../../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

   <!-- Kaiadmin JS -->
   <script src="../../../../assets/js/kaiadmin.min.js"></script>

   <script src="../../../../assets/js/setting-demo.js"></script>
   <script src="../../../../assets/js/demo.js"></script>

   <script src="../../../../assets/js/logout.js"></script>
   <script>
    (function() {
        function showLoadingOverlay() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }
    
        function hideLoadingOverlay() {
            document.getElementById('loading-overlay').style.display = 'none';
        }
    
        document.addEventListener('DOMContentLoaded', hideLoadingOverlay);
        window.addEventListener('beforeunload', function(event) {
            // Only show loading overlay if navigating away from the page
            if (!event.target.activeElement.classList.contains('no-loading')) {
                showLoadingOverlay();
            }
        });
    
        // Attach a click event to export links to prevent the overlay from showing
        document.addEventListener('click', function(event) {
            if (event.target.closest('.export-link')) {
                hideLoadingOverlay();
            }
        });
    })();
    </script>