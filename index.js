// public/js/index.js

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Back to top button
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        // Show button when user scrolls down 300px
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        // Smooth scroll to top when button is clicked
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            if (this.getAttribute('href') !== '#') {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // If mobile menu is open, close it
                    if (navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                    }
                    
                    // Scroll to target
                    window.scrollTo({
                        top: targetElement.offsetTop - 80, // Offset for fixed header
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Animated counters in Statistics section
    const counterElements = document.querySelectorAll('.counter-number');
    
    if (counterElements.length > 0) {
        const animateCounter = (element, target) => {
            let current = 0;
            const increment = target > 1000 ? 50 : (target > 100 ? 1 : 0.1);
            const duration = 2000; // Animation duration in ms
            const steps = Math.ceil(target / increment);
            const stepTime = Math.floor(duration / steps);
            
            const timer = setInterval(() => {
                current += increment;
                
                if (current > target) {
                    element.textContent = numberWithCommas(target);
                    clearInterval(timer);
                } else {
                    element.textContent = numberWithCommas(Math.floor(current));
                }
            }, stepTime);
        };
        
        // Helper function to format numbers with commas
        const numberWithCommas = (x) => {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
        
        // Use Intersection Observer to trigger counter animation when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    animateCounter(entry.target, target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counterElements.forEach(counter => {
            observer.observe(counter);
        });
    }
    
    // Reveal animations for sections when scrolling
    const revealElements = document.querySelectorAll('.feature-card, .testimonial-card, .pricing-card, .how-it-works-step');
    
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        revealElements.forEach(element => {
            element.classList.add('reveal-element'); // Add class for initial state
            revealObserver.observe(element);
        });
    }
    
    // Form submission handler for contact form
    const contactForm = document.querySelector('.contact-form form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic form validation
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');
            
            let isValid = true;
            
            [name, email, subject, message].forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                email.classList.add('error');
            }
            
            if (isValid) {
                // Simulate form submission
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.disabled = true;
                submitBtn.textContent = 'Enviando...';
                
                // Simulate API call
                setTimeout(() => {
                    // Show success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'form-success-message';
                    successMessage.textContent = '¡Mensaje enviado correctamente! Nos pondremos en contacto contigo pronto.';
                    
                    contactForm.reset();
                    contactForm.appendChild(successMessage);
                    
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    
                    // Remove success message after 5 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 5000);
                }, 1500);
            }
        });
    }
    
    // Add CSS for reveal animations
    const style = document.createElement('style');
    style.textContent = `
        .reveal-element {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .reveal-element.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: white;
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                transform: translateY(-150%);
                transition: transform 0.3s ease;
                z-index: 99;
            }
            
            .nav-links.active {
                transform: translateY(0);
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 6rem;
            }
            
            .hero-content {
                max-width: 100%;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .hero-image {
                position: relative;
                top: 0;
                transform: none;
                max-width: 80%;
                margin: 3rem auto 0;
            }
            
            .back-to-top {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 50px;
                height: 50px;
                background: var(--primary-gradient);
                color: white;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 99;
                box-shadow: var(--shadow-md);
            }
            
            .back-to-top.visible {
                opacity: 1;
                visibility: visible;
            }
            
            .back-to-top:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-lg);
            }
            
            .form-error-message {
                color: #ef4444;
                font-size: 14px;
                margin-top: 5px;
            }
            
            .form-success-message {
                background-color: #d1fae5;
                color: #059669;
                padding: 12px;
                border-radius: 8px;
                margin-top: 20px;
                text-align: center;
                animation: fadeIn 0.5s ease;
            }
            
            input.error, textarea.error {
                border-color: #ef4444;
                background-color: #fef2f2;
            }
        }
    `;
    document.head.appendChild(style);
});