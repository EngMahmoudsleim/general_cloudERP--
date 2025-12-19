<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install Inventory Reset Module | {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .install-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        .install-button {
            background: linear-gradient(135deg, #ff416c, #ff4757);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }
        .install-button:hover {
            background: linear-gradient(135deg, #ff3742, #ff2f47);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 65, 108, 0.4);
            color: white;
        }
        .feature-card {
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-10 col-xl-8">
                <div class="card install-card">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-5">
                            <div class="mb-4">
                                <i class="fas fa-refresh" style="font-size: 4rem; color: #667eea;"></i>
                            </div>
                            <h1 class="display-4 text-dark mb-3">Inventory Reset Module</h1>
                            <p class="lead text-muted">Reset all inventory quantities to zero with just a few clicks, making it easy to start fresh and keep your inventory tracking accurate.</p>
                        </div>

                        <!-- Features -->
                        <div class="row mb-5">
                            <div class="col-md-4 mb-4">
                                <div class="text-center feature-card">
                                    <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <h5 class="text-dark">Quick & Easy Reset</h5>
                                    <p class="text-muted small">Reset all inventory quantities to zero with just a few clicks, saving you time and resources.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center feature-card">
                                    <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                                        <i class="fas fa-list-alt"></i>
                                    </div>
                                    <h5 class="text-dark">Flexible Options</h5>
                                    <p class="text-muted small">Choose to reset all products, selected products, or products in specific locations based on your needs.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center feature-card">
                                    <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <h5 class="text-dark">Complete Audit Trail</h5>
                                    <p class="text-muted small">Track all reset operations with detailed logs, reasons, and timestamps for complete transparency.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <div class="bg-light rounded-lg p-4">
                                    <h5 class="text-dark mb-3">Perfect For:</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    Seasonal inventory resets
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    Annual inventory cleanups
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    System migrations
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    Removing obsolete inventory
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    Fresh start scenarios
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success mr-2"></i>
                                                    Audit preparations
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Installation -->
                        <div class="text-center">
                            <div class="alert alert-info d-inline-block">
                                <i class="fas fa-info-circle mr-2"></i>
                                This module will create necessary database tables and set up permissions automatically.
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" class="btn install-button btn-lg" id="installBtn">
                                    <i class="fas fa-download mr-2"></i>
                                    Install Inventory Reset Module
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    Installation typically takes less than 30 seconds
                                </small>
                            </div>
                        </div>

                        <!-- Warning -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Important:</strong> This module provides powerful inventory reset capabilities. 
                                    Make sure to backup your data and assign appropriate permissions to users.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white-50">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Safe, Secure, and Reliable Inventory Management
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#installBtn').click(function() {
                startInstallation();
            });

            function startInstallation() {
                Swal.fire({
                    title: '📦 Install Inventory Reset Module?',
                    html: `
                        <div class="text-left">
                            <p><strong>This will:</strong></p>
                            <ul style="text-align: left; display: inline-block;">
                                <li>Create database tables for reset tracking</li>
                                <li>Set up module permissions</li>
                                <li>Register module routes and views</li>
                            </ul>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> Installation is safe and can be reversed if needed.
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '✅ Yes, Install Module',
                    cancelButtonText: '❌ Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        return fetch('{{ route("inventory-reset.install.execute") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'Installation failed');
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Installation failed: ${error.message}`);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '🎉 Installation Complete!',
                            html: `
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <p><strong>Inventory Reset Module has been installed successfully!</strong></p>
                                    <p class="text-muted">You can now access the module from your dashboard.</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: '🚀 Go to Dashboard',
                            confirmButtonColor: '#007bff',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = '{{ route("inventory-reset.index") }}';
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>