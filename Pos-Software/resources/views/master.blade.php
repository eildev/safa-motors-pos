<!DOCTYPE html>
<html lang="en">

<head>
    @include('body.css')
</head>

<body>
    <div class="main-wrapper">

        <!-- partial:partials/_sidebar.html -->
        @include('body.sidebar')
        <!-- partial -->

        <div class="page-wrapper">

            <!-- partial:partials/_navbar.html -->
            @include('body.navbar')
            <!-- partial -->

            <div class="page-content">
                @yield('admin')
            </div>

            <!-- partial:partials/_footer.html -->
            @include('body.footer')
            <!-- partial -->

        </div>
    </div>
    @include('body.js')
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flexSwitchCheckDefault = document.querySelector('.flexSwitchCheckDefault');
        const form = document.getElementById('darkModeForm');
        if (flexSwitchCheckDefault && form) {
            flexSwitchCheckDefault.addEventListener('change', function() {
                form.submit();
            });
        }

        // nav links active
        const links = document.querySelectorAll('.nav-link');
        links.forEach(link => {
            link.addEventListener('click', function() {
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Handle collapse behavior
                const parentMenu = this.closest('.collapse');
                if (parentMenu) {
                    parentMenu.classList.add('show');
                    parentMenu.previousElementSibling.setAttribute('aria-expanded', 'true');
                    parentMenu.previousElementSibling.classList.remove('collapsed');
                }
            });

            // Ensure parent menus stay open if a child link is active
            if (link.classList.contains('active')) {
                const parentMenu = link.closest('.collapse');
                if (parentMenu) {
                    parentMenu.classList.add('show');
                    parentMenu.previousElementSibling.setAttribute('aria-expanded', 'true');
                    parentMenu.previousElementSibling.classList.remove('collapsed');
                }
            }
        });
    });
    $(function() {
        'use strict'

        if ($(".compose-multiple-select").length) {
            $(".compose-multiple-select").select2();
        }

        /*easymde editor*/
        if ($("#easyMdeEditor").length) {
            var easymde = new EasyMDE({
                element: $("#easyMdeEditor")[0]
            });
        }

    });


    const global_search = document.querySelector("#global_search");
    const search_result = document.querySelector(".search_result");
    // console.log(global_search);
    global_search.addEventListener('keyup', function() {
        // console.log(global_search.value);
        if (global_search.value != '') {
            $.ajax({
                url: '/search/' + global_search.value,
                type: 'GET',
                success: function(res) {
                    // console.log(res);
                    let findData = '';
                    search_result.style.display = 'block';
                    if (res.products.length > 0) {
                        $.each(res.products, function(key, value) {
                            findData += `<tr>
                                    <td>${value.name}</td>
                                    <td>${value.stock}</td>
                                    <td>${value.price}</td>
                                </tr>`
                        });

                        $('.findData').html(findData);
                    } else {
                        $('.table_header').hide();
                        findData += `<tr>
                                    <td colspan = "3" class = "text-center">Data not Found</td>
                                </tr>`
                        $('.findData').html(findData);
                    }
                }
            });
        } else {
            search_result.style.display = 'none';
        }
    })

    global_search.addEventListener('click', function() {
        // console.log(global_search.value);
        if (global_search.value != '') {
            $.ajax({
                url: '/search/' + global_search.value,
                type: 'GET',
                success: function(res) {
                    // console.log(res);
                    let findData = '';
                    search_result.style.display = 'block';
                    if (res.products.length > 0) {
                        $.each(res.products, function(key, value) {
                            findData += `<tr>
                                            <td>${value.name}</td>
                                            <td>${value.stock}</td>
                                            <td>${value.price}</td>
                                        </tr>`
                        });

                        $('.findData').html(findData);
                    } else {
                        $('.table_header').hide();
                        findData += `<tr>
                                        <td colspan = "3" class = "text-center">Data not Found</td>
                                    </tr>`
                        $('.findData').html(findData);
                    }
                }
            });
        } else {
            search_result.style.display = 'none';
        }
    })

    global_search.addEventListener('blur', function() {
        search_result.style.display = 'none';
    });
</script>

</html>
