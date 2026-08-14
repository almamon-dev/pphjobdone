import { useEffect, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Loader2, Eye, EyeOff, Mail, Lock, ArrowRight } from 'lucide-react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-slate-50 relative font-sans p-4">
            <Head title="Admin Portal | Secure Login" />

            <div className="w-full max-w-[380px] relative z-10">
                {/* BRAND LOGO & HEADER */}
                <div className="text-center mb-6 flex flex-col items-center">
                    <h1 className="text-lg font-bold text-slate-900 leading-tight">Admin Login</h1>
                    <p className="text-xs text-slate-500 mt-0.5">Sign in to access your administrative control panel</p>
                </div>
                {/* COMPACT LOGIN CARD */}
                <div className="bg-white rounded-[3px] border border-slate-200/80 shadow-2xs p-6">
                    {status && (
                        <div className="mb-4 text-xs font-semibold text-emerald-600 bg-emerald-50 p-2.5 rounded-[3px] border border-emerald-200">
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-4">
                        {/* EMAIL INPUT */}
                        <div className="space-y-1">
                            <label className="block text-xs font-bold text-slate-700" htmlFor="email">
                                Email Address
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#0a66c2]">
                                    <Mail size={15} />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full h-9 bg-white border ${errors.email ? 'border-rose-400' : 'border-slate-200 focus:border-[#0a66c2]'} rounded-[3px] focus:outline-none focus:ring-0 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400`}
                                    placeholder="Email"
                                    autoComplete="username"
                                    autoFocus
                                />
                            </div>
                            {errors.email && (
                                <p className="text-rose-600 text-[11px] font-medium mt-1">{errors.email}</p>
                            )}
                        </div>

                        {/* PASSWORD INPUT */}
                        <div className="space-y-1">
                            <label className="block text-xs font-bold text-slate-700" htmlFor="password">
                                Password
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#0a66c2]">
                                    <Lock size={15} />
                                </div>
                                <input
                                    id="password"
                                    type={showPassword ? "text" : "password"}
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className={`w-full h-9 bg-white border ${errors.password ? 'border-rose-400' : 'border-slate-200 focus:border-[#0a66c2]'} rounded-[3px] focus:outline-none focus:ring-0 pl-9 pr-9 text-xs text-slate-800 placeholder-slate-400`}
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                />
                                
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                                >
                                    {showPassword ? <EyeOff size={14} /> : <Eye size={14} />}
                                </button>
                            </div>
                            {errors.password && (
                                <p className="text-rose-600 text-[11px] font-medium mt-1">{errors.password}</p>
                            )}
                        </div>

                        {/* REMEMBER ME CHECKBOX */}
                        <div className="flex items-center pt-0.5">
                            <label className="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-4.5 h-4.5 rounded-[3px] border-slate-300 text-[#0a66c2] focus:ring-[#0a66c2]/20 transition-colors cursor-pointer"
                                />
                                <span className="ms-2 text-xs text-slate-600 group-hover:text-slate-900 transition-colors">
                                    Keep me logged in
                                </span>
                            </label>
                        </div>

                        {/* SUBMIT BUTTON */}
                        <div className="pt-2">
                            <button
                                disabled={processing}
                                className="w-full h-9 bg-[#0a66c2] hover:bg-[#084e96] text-white font-semibold rounded-[3px] text-xs transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 shadow-2xs group"
                            >
                                {processing ? (
                                    <Loader2 size={15} className="animate-spin" />
                                ) : (
                                    <>
                                        <span>Enter Admin Portal</span>
                                        <ArrowRight size={14} className="group-hover:translate-x-0.5 transition-transform" />
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
