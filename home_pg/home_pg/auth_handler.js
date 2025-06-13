// Authentication Handler JavaScript
class AuthManager {
    constructor() {
        this.init();
    }

    init() {
        this.checkLoginStatus();
        this.setupEventListeners();
    }

    // Check if user is logged in and update UI
    async checkLoginStatus() {
        try {
            const response = await fetch('auth_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check_login'
            });

            const data = await response.json();
            this.updateAuthUI(data.logged_in, data.user);
        } catch (error) {
            console.error('Error checking login status:', error);
            this.updateAuthUI(false, null);
        }
    }

    // Update authentication UI based on login status
    updateAuthUI(isLoggedIn, user) {
        const authSection = document.getElementById('auth-section');
        
        if (isLoggedIn && user) {
            authSection.innerHTML = `
                <div class="user-menu">
                    <div class="user-info">
                        <span class="user-name">Welcome, ${user.name}</span>
                        <div class="dropdown">
                            <button class="dropdown-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="dropdown-content">
                                <a href="user_dashboard.php">Dashboard</a>
                                <a href="profile.php">Profile</a>
                                <a href="booking_history.php">My Bookings</a>
                                <a href="#" onclick="authManager.logout()">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            authSection.innerHTML = `
                <div class="auth-buttons">
                    <button class="sign-in" onclick="authManager.showLoginModal()">
                        Sign In
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </button>
                    <button class="sign-up" onclick="authManager.showRegisterModal()">Sign Up</button>
                </div>
            `;
        }

        // Add CSS for the new elements
        this.addAuthStyles();
    }

    // Add CSS styles for authentication UI
    addAuthStyles() {
        if (!document.getElementById('auth-styles')) {
            const style = document.createElement('style');
            style.id = 'auth-styles';
            style.textContent = `
                .auth-buttons {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }

                .sign-in, .sign-up {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 20px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }

                .sign-in {
                    background: #3c00a0;
                    color: white;
                }

                .sign-in:hover {
                    background: #290073;
                }

                .sign-up {
                    background: transparent;
                    color: #3c00a0;
                    border: 2px solid #3c00a0;
                }

                .sign-up:hover {
                    background: #3c00a0;
                    color: white;
                }

                .user-menu {
                    position: relative;
                }

                .user-info {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .user-name {
                    color: #333;
                    font-weight: 500;
                }

                .dropdown {
                    position: relative;
                }

                .dropdown-btn {
                    background: #3c00a0;
                    color: white;
                    border: none;
                    padding: 8px 12px;
                    border-radius: 20px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }

                .dropdown-content {
                    display: none;
                    position: absolute;
                    right: 0;
                    background: white;
                    min-width: 160px;
                    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                    border-radius: 8px;
                    z-index: 1000;
                    overflow: hidden;
                }

                .dropdown:hover .dropdown-content {
                    display: block;
                }

                .dropdown-content a {
                    color: #333;
                    padding: 12px 16px;
                    text-decoration: none;
                    display: block;
                    transition: background 0.3s;
                }

                .dropdown-content a:hover {
                    background: #f1f1f1;
                }

                .modal {
                    display: none;
                    position: fixed;
                    z-index: 10000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                }

                .modal-content {
                    background-color: #fefefe;
                    margin: 5% auto;
                    padding: 0;
                    border-radius: 10px;
                    width: 90%;
                    max-width: 450px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                }

                .modal-header {
                    background: #3c00a0;
                    color: white;
                    padding: 20px;
                    border-radius: 10px 10px 0 0;
                    position: relative;
                }

                .modal-header h2 {
                    margin: 0;
                    text-align: center;
                }

                .close {
                    position: absolute;
                    right: 15px;
                    top: 15px;
                    color: white;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }

                .close:hover {
                    opacity: 0.7;
                }

                .modal-body {
                    padding: 30px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 500;
                    color: #333;
                }

                .form-group input, .form-group select {
                    width: 100%;
                    padding: 12px;
                    border: 2px solid #eee;
                    border-radius: 5px;
                    font-size: 14px;
                    transition: border-color 0.3s;
                }

                .form-group input:focus, .form-group select:focus {
                    outline: none;
                    border-color: #3c00a0;
                }

                .btn-primary {
                    width: 100%;
                    background: #3c00a0;
                    color: white;
                    padding: 12px;
                    border: none;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background 0.3s;
                }

                .btn-primary:hover {
                    background: #290073;
                }

                .btn-primary:disabled {
                    background: #ccc;
                    cursor: not-allowed;
                }

                .text-center {
                    text-align: center;
                }

                .mt-3 {
                    margin-top: 15px;
                }

                .alert {
                    padding: 10px;
                    margin-bottom: 15px;
                    border-radius: 5px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .alert-error {
                    background: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .link {
                    color: #3c00a0;
                    cursor: pointer;
                    text-decoration: underline;
                }

                .link:hover {
                    color: #290073;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Show login modal
    showLoginModal() {
        this.createModal('login');
    }

    // Show register modal
    showRegisterModal() {
        this.createModal('register');
    }

    // Create modal for login/register
    createModal(type) {
        // Remove existing modal if any
        const existingModal = document.getElementById('auth-modal');
        if (existingModal) {
            existingModal.remove();
        }

        const modal = document.createElement('div');
        modal.id = 'auth-modal';
        modal.className = 'modal';
        modal.style.display = 'block';

        const isLogin = type === 'login';
        const title = isLogin ? 'Sign In' : 'Sign Up';

        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>${title}</h2>
                    <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="auth-form">
                        <div id="alert-container"></div>
                        ${!isLogin ? `
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                        ` : ''}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        ${!isLogin ? `
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" maxlength="10" required>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age" min="1" max="120" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        ` : ''}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        ${!isLogin ? `
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        ` : ''}
                        ${isLogin ? `
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="remember"> Remember me
                                </label>
                            </div>
                        ` : ''}
                        <button type="submit" class="btn-primary">${title}</button>
                    </form>
                    <div class="text-center mt-3">
                        ${isLogin ? 
                            `Don't have an account? <span class="link" onclick="authManager.showRegisterModal()">Sign Up</span><br>
                             <span class="link" onclick="authManager.showForgotPassword()">Forgot Password?</span>` :
                            `Already have an account? <span class="link" onclick="authManager.showLoginModal()">Sign In</span>`
                        }
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Setup form submission
        document.getElementById('auth-form').addEventListener('submit', (e) => {
            e.preventDefault();
            if (isLogin) {
                this.handleLogin(e.target);
            } else {
                this.handleRegister(e.target);
            }
        });

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Handle login form submission
    async handleLogin(form) {
        const formData = new FormData(form);
        formData.append('action', 'login');

        try {
            const response = await fetch('auth_handler.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            this.showAlert(data.message, data.success ? 'success' : 'error');

            if (data.success) {
                setTimeout(() => {
                    document.getElementById('auth-modal').remove();
                    this.checkLoginStatus();
                    window.location.reload();
                }, 1500);
            }
        } catch (error) {
            this.showAlert('An error occurred. Please try again.', 'error');
        }
    }

    // Handle register form submission
    async handleRegister(form) {
        const formData = new FormData(form);
        
        // Validate password confirmation
        if (formData.get('password') !== formData.get('confirm_password')) {
            this.showAlert('Passwords do not match.', 'error');
            return;
        }

        formData.append('action', 'register');

        try {
            const response = await fetch('simple_register.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            this.showAlert(data.message, data.success ? 'success' : 'error');

            if (data.success) {
                setTimeout(() => {
                    this.showLoginModal();
                }, 2000);
            }
        } catch (error) {
            this.showAlert('An error occurred. Please try again.', 'error');
        }
    }

    // Logout user
    async logout() {
        try {
            const response = await fetch('auth_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            });

            const data = await response.json();
            if (data.success) {
                this.checkLoginStatus();
                window.location.reload();
            }
        } catch (error) {
            console.error('Logout error:', error);
        }
    }

    // Show alert message
    showAlert(message, type) {
        const container = document.getElementById('alert-container');
        if (container) {
            container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        }
    }

    // Setup event listeners
    setupEventListeners() {
        // Phone number validation
        document.addEventListener('input', (e) => {
            if (e.target.name === 'phone') {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
            }
        });
    }

    // Show forgot password modal
    showForgotPassword() {
        // Implementation for forgot password
        alert('Forgot password functionality will be implemented soon!');
    }
}

// Initialize authentication manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.authManager = new AuthManager();
});
