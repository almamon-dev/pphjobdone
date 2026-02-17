<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\SendOtp;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    use ApiResponse, SendOtp;

    public function registerApi(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Step 3: Send OTP safely
            try {
                $otp = $this->sendOtp($user, 'Verify Your Email Address');
            } catch (Exception $smtpError) {
                Log::error("SMTP Error during registration: {$smtpError->getMessage()}");
                DB::rollBack();

                return $this->sendError(
                    'Registration failed due to email server issue. Please Contact Support.',
                    [],
                    500
                );
            }

            DB::commit(); // Commit transaction if everything succeeds

            $message = __('Register Successfully. Please check your email to verify. OTP: ').$otp;

            return $this->sendResponse(new RegisterResource($user), $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: '.$e->getMessage(), ['context' => $request->all()]);

            return $this->sendError('Registration failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function loginApi(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid Credentials', [], 401);
            }

            if (! $user->email_verified_at) {
                return $this->sendError('Email Not Verified', [], 403);
            }
            $token = $user->createToken('YourAppName')->plainTextToken;

            return $this->sendResponse(new LoginResource($user), 'Login successful', $token);
        } catch (Exception $e) {
            Log::error('Login Error: '.$e->getMessage());

            return $this->sendError('Login failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function verifyEmailApi(VerifyEmailRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->firstOrFail();
            Log::info('Verify Email Attempt', ['email' => $request->email, 'otp' => $request->otp]);
            // Check if email is already verified
            if ($user->email_verified_at && $user->is_verified) {
                Log::info('Email already verified', ['user_id' => $user->id, 'email' => $user->email]);
                DB::rollBack();

                return $this->sendError('Email is already verified', [], 422);
            }
            $otp = $user->otps()
                ->where('otp', $request->otp)
                ->where('is_verified', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (! $otp) {
                DB::rollBack();

                return $this->sendError('Invalid or expired OTP', [], 422);
            }
            $otp->update(['is_verified' => true, 'verified_at' => now()]);
            $user->update([
                'email_verified_at' => now(),
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            $token = $user->createToken('YourAppName')->plainTextToken;

            DB::commit();

            return $this->sendResponse(new LoginResource($user), 'Email verified successfully', $token);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Email Verification Error: '.$e->getMessage());

            return $this->sendError('Verification failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function resendOtpApi(ResendOtpRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $otp = $this->sendOtp($user, 'Resend OTP for Email Verification');

            return $this->sendResponse(new RegisterResource($user), 'OTP resent successfully. OTP: '.$otp);
        } catch (Exception $e) {
            Log::error('Resend OTP Error: '.$e->getMessage());

            return $this->sendError('OTP resend failed', ['error' => $e->getMessage()], 500);
        }
    }

    public function forgotPasswordApi(ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $otp = $this->sendOtp($user, 'Reset Your Password');

            return $this->sendResponse(new RegisterResource($user), 'OTP sent for password reset. OTP: '.$otp);
        } catch (Exception $e) {
            Log::error('Forgot Password Error: '.$e->getMessage());

            return $this->sendError('Failed to send OTP', ['error' => $e->getMessage()], 500);
        }
    }

    public function verifyOtpApi(VerifyOtpRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $otp = $user->otps()
                ->where('otp', $request->otp)
                ->where('is_verified', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (! $otp) {
                return $this->sendError('Invalid or expired OTP', [], 422);
            }

            $otp->update(['is_verified' => true, 'verified_at' => now()]);
            $token = Str::random(40);
            $user->update([
                'reset_password_token' => $token,
                'reset_password_token_expire_at' => now()->addHour(),
            ]);

            return $this->sendResponse(new RegisterResource($user), 'OTP verified. Token generated for password reset.', $token);
        } catch (Exception $e) {
            Log::error('OTP Verification Error: '.$e->getMessage());

            return $this->sendError('Failed to verify OTP', ['error' => $e->getMessage()], 500);
        }
    }

    public function resetPasswordApi(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->firstOrFail();

            if (
                $user->reset_password_token !== $request->token ||
                Carbon::now()->gt($user->reset_password_token_expire_at)
            ) {
                return $this->sendError('Invalid or expired token', [], 401);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'reset_password_token' => null,
                'reset_password_token_expire_at' => null,
            ]);

            return $this->sendResponse([], 'Password reset successfully.');
        } catch (Exception $e) {
            Log::error('Reset Password Error: '.$e->getMessage());

            return $this->sendError('Failed to reset password', ['error' => $e->getMessage()], 500);
        }
    }

    public function logoutApi(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $user->update(['is_online' => false]);
            $user->currentAccessToken()->delete();

            return $this->sendResponse([], 'Logout successful.');
        } catch (Exception $e) {
            Log::error('Logout Error: '.$e->getMessage());

            return $this->sendError('Logout failed', ['error' => 'Please try again'], 500);
        }
    }

    public function updateStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|boolean',
            ]);

            $user = auth()->user();
            $user->update(['is_online' => $request->status]);

            return $this->sendResponse(new LoginResource($user), 'Status updated successfully.');
        } catch (Exception $e) {
            Log::error('Status Update Error: '.$e->getMessage());

            return $this->sendError('Status update failed', ['error' => $e->getMessage()], 500);
        }
    }
}
