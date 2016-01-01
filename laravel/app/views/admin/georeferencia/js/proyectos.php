<script type="text/javascript">
    $(document).ready(function() {
        $("[data-toggle='offcanvas']").click();
        
        var proymap = Psigeo.map('mymap');
        
        // Variables
        var objMain = $('#main');
        
        // Show sidebar
        function showSidebar() {
            objMain.addClass('use-sidebar');
            Psigeo.mapResize(proymap);
        }

        // Hide sidebar
        function hideSidebar() {
            objMain.removeClass('use-sidebar');
            Psigeo.mapResize(proymap);
        }

        // Sidebar separator
        var objSeparator = $('#separator');

        objSeparator.click(function(e) {
            e.preventDefault();
            if (objMain.hasClass('use-sidebar')) {
                hideSidebar();
            }
            else {
                showSidebar();
            }
        }).css('height', objSeparator.parent().outerHeight() + 'px');
        
        
    });

</script>