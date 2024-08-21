@extends('index')
@section('main')
    <div class="main">
        <div class="container pb-5">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="#">Liên hệ</a></li>
                    </ol>
                </div>
            </nav>

            <div class="contact-page">

                <div class="contact-container">
                    <h2 class="contact-title">Liên hệ với cửa hàng</h2>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form id="contactForm" action="{{ route('contact.send') }}" method="POST" class="contact-form">
                        @csrf <!-- Thêm CSRF token để bảo mật -->
                        <div class="contact-form-group">
                            <label for="name" class="contact-label">Họ và tên:</label>
                            <input type="text" id="name" name="name" class="contact-input" required>
                            <div class="error" id="nameError"></div>
                        </div>
                        <div class="contact-form-group">
                            <label for="email" class="contact-label">Email:</label>
                            <input type="email" id="email" name="email" class="contact-input" required>
                            <div class="error" id="emailError"></div>
                        </div>
                        <div class="contact-form-group">
                            <label for="subject" class="contact-label">Chủ đề:</label>
                            <input type="text" id="subject" name="subject" class="contact-input" required>
                            <div class="error" id="subjectError"></div>
                        </div>
                        <div class="contact-form-group">
                            <label for="message" class="contact-label">Nội dung:</label>
                            <textarea id="message" name="message" class="contact-textarea" rows="5" required></textarea>
                            <div class="error" id="messageError"></div>
                        </div>
                        <button type="submit" class="contact-btn-submit">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function (event) {
            let hasError = false;

            // Clear previous errors
            document.querySelectorAll('.error').forEach(function (el) {
                el.textContent = '';
            });

            // Validate name
            const name = document.getElementById('name').value;
            if (name.trim() === '') {
                document.getElementById('nameError').textContent = 'Vui lòng nhập họ và tên.';
                hasError = true;
            }

            // Validate email
            const email = document.getElementById('email').value;
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (email.trim() === '') {
                document.getElementById('emailError').textContent = 'Vui lòng nhập email.';
                hasError = true;
            } else if (!emailPattern.test(email)) {
                document.getElementById('emailError').textContent = 'Email không hợp lệ.';
                hasError = true;
            }

            // Validate subject
            const subject = document.getElementById('subject').value;
            if (subject.trim() === '') {
                document.getElementById('subjectError').textContent = 'Vui lòng nhập chủ đề.';
                hasError = true;
            }

            // Validate message
            const message = document.getElementById('message').value;
            if (message.trim() === '') {
                document.getElementById('messageError').textContent = 'Vui lòng nhập nội dung.';
                hasError = true;
            }

            // Prevent form submission if there are errors
            if (hasError) {
                event.preventDefault();
            }
        });
    </script>
@endsection
