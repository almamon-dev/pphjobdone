import { useEffect, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Loader2, Eye, EyeOff, ShieldCheck, Mail, Lock, ArrowRight } from 'lucide-react';

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

            {/* Subtle background decoration */}
            <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div className="absolute -top-24 -right-24 w-96 h-96 bg-[#0a66c2]/5 rounded-full blur-3xl"></div>
                <div className="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl"></div>
            </div>

            <div className="w-full max-w-[400px] relative z-10">
                {/* Admin Header Context */}
                <div className="text-center mb-8">
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight">Admin Portal</h1>
                    <p className="text-sm text-slate-500 mt-1">Sign in to access the control panel</p>
                </div>

                {/* Login Card */}
                <div className="bg-white rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8">
                    <form onSubmit={submit} className="space-y-5">
                        
                        {/* Email Input */}
                        <div className="space-y-1.5">
                            <label className="text-xs font-bold text-slate-600 uppercase tracking-wider" htmlFor="email">
                                Email Address
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#0a66c2] transition-colors">
                                    <Mail size={16} />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full bg-slate-50/50 border ${errors.email ? 'border-red-400' : 'border-slate-200'} rounded focus:bg-white focus:ring-2 focus:ring-[#0a66c2]/20 focus:border-[#0a66c2] pl-10 pr-4 py-2.5 text-[14px] text-slate-800 placeholder-slate-400 outline-none transition-all`}
                                    placeholder="admin@example.com"
                                    autoComplete="username"
                                    autoFocus
                                />
                            </div>
                            {errors.email && (
                                <p className="text-red-500 text-[11px] font-medium mt-1">{errors.email}</p>
                            )}
                        </div>

                        {/* Password Input */}
                        <div className="space-y-1.5">
                            <div className="flex justify-between items-center">
                                <label className="text-xs font-bold text-slate-600 uppercase tracking-wider" htmlFor="password">
                                    Password
                                </label>
                            </div>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#0a66c2] transition-colors">
                                    <Lock size={16} />
                                </div>
                                <input
                                    id="password"
                                    type={showPassword ? "text" : "password"}
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className={`w-full bg-slate-50/50 border ${errors.password ? 'border-red-400' : 'border-slate-200'} rounded focus:bg-white focus:ring-2 focus:ring-[#0a66c2]/20 focus:border-[#0a66c2] pl-10 pr-10 py-2.5 text-[14px] text-slate-800 placeholder-slate-400 outline-none transition-all`}
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                />
                                
                                {/* Password Visibility Toggle */}
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                                >
                                    {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                                </button>
                            </div>
                            {errors.password && (
                                <p className="text-red-500 text-[11px] font-medium mt-1">{errors.password}</p>
                            )}
                        </div>

                        {/* Keep me logged in (Checkbox) */}
                        <div className="flex items-center pt-1">
                            <label className="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-4 h-4 rounded border-slate-300 text-[#0a66c2] focus:ring-[#0a66c2]/30 transition-colors cursor-pointer"
                                />
                                <span className="ms-2 text-[12px] font-medium text-slate-500 group-hover:text-slate-700 transition-colors">
                                    Keep me securely logged in
                                </span>
                            </label>
                        </div>

                        {/* Submit Button */}
                        <div className="pt-3">
                            <button
                                disabled={processing}
                                className="w-full bg-[#0a66c2] hover:bg-[#0855a3] text-white font-bold py-3 rounded transition-all duration-300 disabled:opacity-60 flex items-center justify-center gap-2 shadow-lg shadow-[#0a66c2]/20 group"
                            >
                                {processing ? (
                                    <Loader2 size={18} className="animate-spin" />
                                ) : (
                                    <>
                                        <span className="text-[14px] tracking-wide">Enter Admin Portal</span>
                                        <ArrowRight size={16} className="group-hover:translate-x-1 transition-transform" />
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
