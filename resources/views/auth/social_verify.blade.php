@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Verify Your Email</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <!-- Alternative Verification Methods -->
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Can't find the verification code?</h6>
                        <p class="mb-2">Don't worry! Here are some tips to find your verification code:</p>
                        <ul class="mb-2">
                            <li><strong>Check Spam/Junk folder</strong> in your email</li>
                            <li><strong>Wait a few minutes</strong> - email delivery can be delayed</li>
                            <li><strong>Try resending</strong> - use the resend button below</li>
                            <li><strong>Check all email folders</strong> including Promotions tab</li>
                        </ul>
                        <small class="text-muted">If you still can't find the code, contact our support team.</small>
                    </div>
                    
                    <!-- Forgot Email Account Help -->
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> Forgot your email account?</h6>
                        <p class="mb-2">If you can't remember which email account you used:</p>
                        <ul class="mb-2">
                            <li><strong>Check all your email accounts</strong> - Gmail, Yahoo, Outlook, etc.</li>
                            <li><strong>Look for "JJ Flowershop" emails</strong> in your inbox</li>
                            <li><strong>Check email on your phone</strong> - sometimes easier to find</li>
                            <li><strong>Try common email addresses</strong> you might have used</li>
                        </ul>
                        <div class="text-center">
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#emailHelpModal">
                                <i class="bi bi-question-circle"></i> Need Help Finding Your Email?
                            </button>
                        </div>
                    </div>
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('social.verify.code') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="verification_code">Enter the 6-digit verification code sent to your email:</label>
                            <input type="text" name="verification_code" id="verification_code" class="form-control" maxlength="6" required autofocus placeholder="123456">
                            <small class="form-text text-muted">Check your email inbox and spam folder</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Verify</button>
                    </form>
                    
                    <!-- Resend Code Options -->
                    <div class="text-center">
                        <p class="text-muted mb-2">Didn't receive the code?</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('social.resend.email') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope"></i> Resend to Email
                            </a>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#helpModal">Need help?</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">Need Help with Verification?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Common Solutions:</h6>
                <ul>
                    <li><strong>Check Spam/Junk folder</strong> - Sometimes emails go there</li>
                    <li><strong>Check Promotions tab</strong> - Gmail sometimes puts emails there</li>
                    <li><strong>Wait a few minutes</strong> - Email delivery can be delayed</li>
                    <li><strong>Try resending</strong> - Use the resend button above</li>
                    <li><strong>Check all email folders</strong> - Including Trash</li>
                </ul>
                
                <h6>Still having trouble?</h6>
                <p>Contact our support team:</p>
                <ul>
                    <li><strong>Phone:</strong> 09674184857</li>
                    <li><strong>Email:</strong> jjflowershopph@gmail.com</li>
                    <li><strong>Facebook:</strong> @jjflowershop_</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Email Help Modal -->
<div class="modal fade" id="emailHelpModal" tabindex="-1" aria-labelledby="emailHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailHelpModalLabel">Help Finding Your Email Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-search"></i> How to Find Your Email:</h6>
                        <ol>
                            <li><strong>Check your phone</strong> - Look for email apps (Gmail, Yahoo, Outlook)</li>
                            <li><strong>Search for "JJ Flowershop"</strong> in your email</li>
                            <li><strong>Check all email accounts</strong> you might have used</li>
                            <li><strong>Look in Spam/Junk folders</strong></li>
                            <li><strong>Check Promotions tab</strong> (Gmail)</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-lightbulb"></i> Common Email Providers:</h6>
                        <ul>
                            <li><strong>Gmail:</strong> @gmail.com</li>
                            <li><strong>Yahoo:</strong> @yahoo.com, @yahoo.com.ph</li>
                            <li><strong>Outlook:</strong> @outlook.com, @hotmail.com</li>
                            <li><strong>Work Email:</strong> @company.com</li>
                            <li><strong>School Email:</strong> @school.edu</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="bi bi-info-circle"></i> Still Can't Find It?</h6>
                    <p class="mb-2">If you absolutely can't find your email account, we can help you manually verify your account:</p>
                    <ul class="mb-2">
                        <li><strong>Contact our support team</strong> with your Facebook/Google account details</li>
                        <li><strong>Provide your full name</strong> as it appears on your social media</li>
                        <li><strong>We'll manually verify</strong> your account for you</li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <h6>Contact Support for Manual Verification:</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Phone:</strong><br>
                            <a href="tel:09674184857" class="btn btn-outline-primary btn-sm">09674184857</a>
                        </div>
                        <div class="col-md-4">
                            <strong>Email:</strong><br>
                            <a href="mailto:jjflowershopph@gmail.com" class="btn btn-outline-success btn-sm">jjflowershopph@gmail.com</a>
                        </div>
                        <div class="col-md-4">
                            <strong>Facebook:</strong><br>
                            <a href="https://facebook.com/jjflowershop_" target="_blank" class="btn btn-outline-info btn-sm">@jjflowershop_</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Found My Email</button>
            </div>
        </div>
    </div>
</div>
@endsection 