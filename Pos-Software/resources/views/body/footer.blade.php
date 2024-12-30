 <!-- partial:partials/_footer.html -->
 <div id="myfooter">
     <footer
         class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
         <p class="text-muted mb-1 mb-md-0">Copyright © <span id="currentYear"></span> <a
                 href="https://electro-pos.eclipseintellitech.com/login" target="_blank">EIL
                 Electro</a>.</p>
         <p class="text-muted">Powered by <a href="https://eclipseintellitech.com/" target="_blank">Eclipse Intellitech
                 Limited.<i class="mb-1 text-primary ms-1 icon-sm" data-feather="heart"></i></a></p>
     </footer>
 </div>
 <!-- partial -->
 <script>
     // Get the current year
     var currentYear = new Date().getFullYear();

     // Set the current year in the span element
     document.getElementById('currentYear').innerText = currentYear;
 </script>
