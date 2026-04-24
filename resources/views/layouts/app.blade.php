<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven | @yield('title', 'Welcome')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    @include('partials.navbar')

    @yield('content')

    @include('partials.auth-modal')

    <script>
        // Universal Modal Functions (with Scrollbar Jump Fix)
        function openModal(modalId) {
            // Check if the modal exists to prevent errors
            const modal = document.getElementById(modalId);
            if (!modal) return;

            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.paddingRight = scrollbarWidth + 'px';
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.remove('active');
            document.body.style.overflow = 'auto'; 
            document.body.style.paddingRight = '0px';
        }

        // Closes the modal if you click the dark background behind it
        function closeOnOutsideClick(event, modalId) {
            if (event.target.id === modalId) {
                closeModal(modalId);
            }
        }

        // Switches between the "Log In" and "Sign Up" tabs
        function switchAuthTab(tabName) {
            // 1. Remove 'active' from both tabs and forms
            document.getElementById('tab-login').classList.remove('active');
            document.getElementById('tab-register').classList.remove('active');
            document.getElementById('form-login').classList.remove('active');
            document.getElementById('form-register').classList.remove('active');

            // 2. Add 'active' only to the selected tab and form
            document.getElementById('tab-' + tabName).classList.add('active');
            document.getElementById('form-' + tabName).classList.add('active');
        }

        // Filter Chip UI Toggle (For the Browse Page)
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                this.parentElement.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
</body>
</html>