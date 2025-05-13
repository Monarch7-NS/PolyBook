// Script principal pour PolyBook

document.addEventListener('DOMContentLoaded', function() {
    // Notifications temporaires (auto-disparition)
    const notifications = document.querySelectorAll('.notification:not(.persistent)');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 500);
        }, 5000);
    });

    // Afficher/masquer les spoilers
    const spoilerButtons = document.querySelectorAll('.show-spoiler');
    spoilerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.review-card, .feed-item');
            card.querySelector('.review-content, .review-text').classList.remove('hidden');
            card.querySelector('.spoiler-alert').classList.add('hidden');
        });
    });

    // Afficher/masquer les formulaires de commentaire
    const commentButtons = document.querySelectorAll('.btn-comment');
    commentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review');
            const form = document.getElementById('comment-form-' + reviewId);
            if (form) {
                form.classList.toggle('hidden');
            }
        });
    });

    // Validation des formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('error');
                    
                    // Créer un message d'erreur si non existant
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.classList.add('error-message');
                        errorMsg.textContent = 'Ce champ est requis';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('error');
                    
                    // Supprimer le message d'erreur s'il existe
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });

            if (!valid) {
                event.preventDefault();
            }
        });
    });

    // Validation des mots de passe dans le formulaire d'inscription
    const registrationForm = document.querySelector('form[action*="inscription"]');
    if (registrationForm) {
        const password = registrationForm.querySelector('input[name="password"]');
        const confirmPassword = registrationForm.querySelector('input[name="confirm_password"]');
        
        confirmPassword.addEventListener('input', function() {
            if (this.value !== password.value) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
        
        password.addEventListener('input', function() {
            if (confirmPassword.value !== '' && confirmPassword.value !== this.value) {
                confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });
    }

    // Gestion des modales
    const modals = document.querySelectorAll('.modal');
    const modalButtons = document.querySelectorAll('[id$="-btn"]');
    const closeButtons = document.querySelectorAll('.close');
    
    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.id.replace('-btn', '-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
            }
        });
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
        });
    });
    
    window.addEventListener('click', function(event) {
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activer l'onglet sélectionné
            this.classList.add('active');
            document.getElementById(tabId)?.classList.add('active');
        });
    });

    // Recherche avancée
    const advancedSearchToggle = document.querySelector('.advanced-search-toggle');
    const advancedSearchForm = document.querySelector('.advanced-search-form');
    
    if (advancedSearchToggle && advancedSearchForm) {
        advancedSearchToggle.addEventListener('click', function() {
            advancedSearchForm.classList.toggle('active');
            this.textContent = advancedSearchForm.classList.contains('active') 
                ? 'Masquer la recherche avancée' 
                : 'Afficher la recherche avancée';
        });
    }

    // Confirmation de suppression
    const deleteActions = document.querySelectorAll('[data-confirm]');
    
    deleteActions.forEach(action => {
        action.addEventListener('click', function(event) {
            const confirmation = confirm(this.getAttribute('data-confirm'));
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });

    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Formulaire de notation avec étoiles
    const ratingInputs = document.querySelectorAll('.rating-input');
    
    ratingInputs.forEach(input => {
        const stars = input.querySelectorAll('label');
        const ratingDisplay = document.createElement('div');
        ratingDisplay.classList.add('rating-display');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const ratingValue = this.getAttribute('for').replace('star', '');
                const ratingText = `${ratingValue} étoile${ratingValue > 1 ? 's' : ''}`;
                ratingDisplay.textContent = ratingText;
                input.appendChild(ratingDisplay);
            });
            
            star.addEventListener('mouseout', function() {
                if (ratingDisplay.parentNode === input) {
                    input.removeChild(ratingDisplay);
                }
            });
        });
    });

    // Menu mobile (responsive)
    const mobileMenuButton = document.createElement('button');
    mobileMenuButton.classList.add('mobile-menu-button');
    mobileMenuButton.innerHTML = '<i class="fas fa-bars"></i>';
    
    const header = document.querySelector('.site-header .container');
    const mainNav = document.querySelector('.main-nav');
    
    if (header && mainNav) {
        window.addEventListener('resize', toggleMobileMenu);
        toggleMobileMenu();
        
        function toggleMobileMenu() {
            if (window.innerWidth <= 768) {
                if (!document.querySelector('.mobile-menu-button')) {
                    header.insertBefore(mobileMenuButton, mainNav);
                    mainNav.style.display = 'none';
                    
                    mobileMenuButton.addEventListener('click', function() {
                        if (mainNav.style.display === 'none') {
                            mainNav.style.display = 'block';
                            this.innerHTML = '<i class="fas fa-times"></i>';
                        } else {
                            mainNav.style.display = 'none';
                            this.innerHTML = '<i class="fas fa-bars"></i>';
                        }
                    });
                }
            } else {
                const button = document.querySelector('.mobile-menu-button');
                if (button) {
                    button.remove();
                }
                mainNav.style.display = '';
            }
        }
    }

    // Fonction pour ouvrir/fermer un accordéon
    const accordionButtons = document.querySelectorAll('.accordion-button');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // Prévisualisation des images pour les formulaires d'upload
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.querySelector('.image-preview');
            
            if (preview) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
});