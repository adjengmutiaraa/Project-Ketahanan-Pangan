// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    
    if(mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.nav-menu') && !event.target.closest('.mobile-menu-btn')) {
            navMenu.classList.remove('active');
        }
    });

    // Statistics animation
    function animateCounter(element, target, duration) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }
        
        updateCounter();
    }

    // Animate statistics when they come into view
    const statsGrid = document.querySelector('.stats-grid');
    if(statsGrid) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const totalKecamatan = document.getElementById('total-kecamatan');
                    const totalKomoditas = document.getElementById('total-komoditas');
                    const totalPengaduan = document.getElementById('total-pengaduan');
                    
                    if(totalKecamatan) animateCounter(totalKecamatan, 15, 1000);
                    if(totalKomoditas) animateCounter(totalKomoditas, 42, 1000);
                    if(totalPengaduan) animateCounter(totalPengaduan, 127, 1000);
                    
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(statsGrid);
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'var(--danger)';
                    
                    // Add error message
                    let errorMsg = field.parentNode.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = 'var(--danger)';
                        errorMsg.style.fontSize = '0.875rem';
                        errorMsg.style.marginTop = '5px';
                        field.parentNode.appendChild(errorMsg);
                    }
                    errorMsg.textContent = 'Field ini wajib diisi';
                } else {
                    field.style.borderColor = '';
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (errorMsg) errorMsg.remove();
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Dynamic filter form submission
    const filterForms = document.querySelectorAll('.filter-form');
    filterForms.forEach(form => {
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                form.submit();
            });
        });
    });

    // --- Animasi Garis Bawah Saat Klik (Click Effect) ---
const navLinks = document.querySelectorAll('.nav-menu a');

navLinks.forEach(link => {
    // Jangan terapkan efek klik pada tombol LOGIN ADMIN
    if (!link.classList.contains('btn')) {
        link.addEventListener('click', function(e) {
            // Hapus kelas 'active' dan 'js-clicked' dari semua link lain
            navLinks.forEach(nav => {
                nav.classList.remove('js-clicked');
                nav.classList.remove('active');
                // Hapus border-bottom CSS default agar tidak konflik
                nav.style.borderBottom = '2px solid transparent'; 
            });

            // Tambahkan kelas ke link yang baru diklik
            this.classList.add('js-clicked');
            this.classList.add('active');
            
            // Tambahkan kembali border-bottom untuk mempertahankan warna teks saat diklik
            this.style.borderBottom = '2px solid var(--secondary)'; 
            
            // Untuk mencegah navigasi langsung dan memberikan waktu animasi (Opsional)
            // Namun, karena ini adalah navigasi, kita biarkan navigasi tetap berjalan
            // Jika Anda ingin animasi penuh, Anda harus menggunakan e.preventDefault() 
            // dan menavigasi secara manual setelah animasi selesai (lebih kompleks).
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // ... (Kode Mobile Menu, Button Press, Animasi Garis Bawah yang sudah ada) ...

    // --- SCROLL-TRIGGERED ANIMATION (Intersection Observer) ---
    
    // Targetkan semua section, grid footer, dan copyright
    const animatedElements = document.querySelectorAll(
        '.section, footer .footer-grid, footer .copyright'
    );

    const observerOptions = {
        root: null, // Menggunakan viewport
        rootMargin: '0px 0px -100px 0px', // Pemicu 100px sebelum elemen mencapai dasar viewport
        threshold: 0 // Pemicu sesegera mungkin
    };

    const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Jika elemen terlihat, tambahkan kelas animasi
                entry.target.classList.add('is-animated');
                // Hentikan pengamatan setelah terpicu
                observer.unobserve(entry.target);
            }
        });
    };

    const observer = new IntersectionObserver(observerCallback, observerOptions);
    
    // Mulai mengamati setiap elemen yang ditargetkan
    animatedElements.forEach(element => {
        // Jangan menerapkan animasi pada hero section (sudah terlihat)
        if (!element.classList.contains('hero')) {
            observer.observe(element);
        }
    });
});
})