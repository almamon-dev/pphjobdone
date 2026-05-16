import { useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Mail, Lock, ChevronRight, Loader2, ShieldCheck } from 'lucide-react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

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
        <div className="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#080808] selection:bg-orange-500/30">
            <Head title="Admin Access | PPH Job Done" />

            {/* Subtle Ambient Background */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-orange-600/[0.03] blur-[120px]" />
                <div className="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-blue-600/[0.03] blur-[120px]" />
            </div>

            <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className="relative z-10 w-full max-w-[420px] px-6"
            >
                {/* Header */}
                <div className="text-center mb-10">
                    <motion.div
                        initial={{ scale: 0.9 }}
                        animate={{ scale: 1 }}
                        className="inline-flex items-center justify-center w-12 h-12 rounded bg-orange-500/10 border border-orange-500/20 mb-4"
                    >
                        <ShieldCheck className="w-6 h-6 text-orange-500" />
                    </motion.div>
                    <h1 className="text-2xl font-semibold text-white tracking-tight">Admin Portal</h1>
                    <p className="text-gray-500 text-sm mt-1.5">Secure access to your dashboard</p>
                </div>

                {/* Login Card */}
                <div className="bg-white/[0.02] backdrop-blur-xl border border-white/[0.06] rounded p-8 shadow-2xl">
                    <form onSubmit={submit} className="space-y-6">
                        {/* Email */}
                        <div className="space-y-2">
                            <label className="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.1em] ml-1" htmlFor="email">
                                Email Address
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-600 group-focus-within:text-orange-500 transition-colors">
                                    <Mail className="w-4 h-4" />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    className={`w-full bg-white/[0.03] border ${errors.email ? 'border-red-500/40' : 'border-white/[0.08]'} group-hover:border-white/[0.15] focus:border-orange-500/50 focus:ring-0 rounded pl-11 pr-4 py-3.5 text-white placeholder-gray-700 transition-all duration-300 outline-none text-sm`}
                                    placeholder="admin@pphjobdone.com"
                                    onChange={(e) => setData('email', e.target.value)}
                                    autoComplete="username"
                                    autoFocus
                                />
                            </div>
                            {errors.email && (
                                <p className="text-red-400 text-[11px] ml-1">{errors.email}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div className="space-y-2">
                            <div className="flex items-center justify-between px-1">
                                <label className="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.1em]" htmlFor="password">
                                    Password
                                </label>
                                {/* {canResetPassword && (
                                    <Link
                                        href={route('password.request')}
                                        className="text-[11px] font-medium text-orange-500 hover:text-orange-400 transition-colors"
                                    >
                                        Forgot?
                                    </Link>
                                )} */}
                            </div>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-600 group-focus-within:text-orange-500 transition-colors">
                                    <Lock className="w-4 h-4" />
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    className={`w-full bg-white/[0.03] border ${errors.password ? 'border-red-500/40' : 'border-white/[0.08]'} group-hover:border-white/[0.15] focus:border-orange-500/50 focus:ring-0 rounded pl-11 pr-4 py-3.5 text-white placeholder-gray-700 transition-all duration-300 outline-none text-sm`}
                                    placeholder="••••••••"
                                    onChange={(e) => setData('password', e.target.value)}
                                    autoComplete="current-password"
                                />
                            </div>
                            {errors.password && (
                                <p className="text-red-400 text-[11px] ml-1">{errors.password}</p>
                            )}
                        </div>

                        {/* Remember Me */}
                        <div className="flex items-center pt-1">
                            <label className="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="sr-only"
                                />
                                <div className={`w-4 h-4 rounded border border-white/[0.1] transition-all flex items-center justify-center ${data.remember ? 'bg-orange-500 border-orange-500 shadow-[0_0_10px_rgba(249,115,22,0.3)]' : 'bg-white/[0.02]'}`}>
                                    {data.remember && (
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="4" className="w-2.5 h-2.5 text-white">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    )}
                                </div>
                                <span className="ms-2.5 text-xs text-gray-500 group-hover:text-gray-400 transition-colors">Keep me logged in</span>
                            </label>
                        </div>

                        {/* Submit Button */}
                        <button
                            disabled={processing}
                            className="group relative w-full bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white font-semibold py-3.5 rounded transition-all duration-300 shadow-xl shadow-orange-500/10 overflow-hidden flex items-center justify-center gap-2"
                        >
                            {processing ? (
                                <Loader2 className="w-5 h-5 animate-spin" />
                            ) : (
                                <>
                                    <span className="text-sm">Enter Dashboard</span>
                                    <ChevronRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                                </>
                            )}
                        </button>
                    </form>

                    {/* Quick Access Credentials */}
                    <div className="mt-8 pt-8 border-t border-white/[0.05]">
                        <p className="text-[10px] text-gray-600 text-center uppercase tracking-[0.2em] mb-4">Development Access</p>
                        <button
                            onClick={() => {
                                setData({
                                    ...data,
                                    email: 'admin@gmail.com',
                                    password: 'password'
                                });
                            }}
                            className="group w-full flex flex-col items-center gap-1.5 py-3 rounded bg-white/[0.02] border border-white/[0.05] hover:border-orange-500/30 hover:bg-orange-500/[0.02] transition-all duration-300"
                        >
                            <span className="text-xs text-gray-500 group-hover:text-gray-300 transition-colors">admin@gmail.com</span>
                            <span className="text-[10px] text-gray-600 group-hover:text-orange-500/60 transition-colors font-medium tracking-tight">password: password</span>
                        </button>
                    </div>
                </div>

                {/* Footer */}
                <footer className="mt-12">
                    <p className="text-gray-700 text-[9px] tracking-[0.3em] uppercase text-center">
                        &copy; {new Date().getFullYear()} PPH Job Done
                    </p>
                </footer>
            </motion.div>
        </div>
    );
}
