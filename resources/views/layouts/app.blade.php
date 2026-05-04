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
        // Tab switcher (used on pet profile page)
        function switchTab(tabId, buttonElement) {
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            document.querySelectorAll('.pet-tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');
            buttonElement.classList.add('active');
        }

        // Universal Modal Functions (with Scrollbar Jump Fix)
        function openModal(modalId) {
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

        function closeOnOutsideClick(event, modalId) {
            if (event.target.id === modalId) {
                closeModal(modalId);
            }
        }

        function switchAuthTab(tabName) {
            document.getElementById('tab-login').classList.remove('active');
            document.getElementById('tab-register').classList.remove('active');
            document.getElementById('form-login').classList.remove('active');
            document.getElementById('form-register').classList.remove('active');
            document.getElementById('tab-' + tabName).classList.add('active');
            document.getElementById('form-' + tabName).classList.add('active');
        }

        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                this.parentElement.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });

        @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openModal('authBackdrop');
            @if (old('_form') === 'register')
            switchAuthTab('register');
            @endif
        });
        @endif
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>