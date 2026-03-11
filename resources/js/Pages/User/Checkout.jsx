import React, { useState, useEffect } from "react";
import { Head, usePage } from "@inertiajs/react";
import { loadStripe } from "@stripe/stripe-js";
import {
    Elements,
    CardNumberElement,
    CardExpiryElement,
    CardCvcElement,
    useStripe,
    useElements,
} from "@stripe/react-stripe-js";
import {
    ShieldCheck,
    Lock,
    User,
    Mail,
    CreditCard,
    Calendar,
    Hash,
    CheckCircle2,
    Loader2,
} from "lucide-react";
import { motion } from "framer-motion";
import axios from "axios";

const stripeStyles = {
    base: {
        fontSize: "16px",
        color: "#ffffff",
        fontFamily: "'Inter', sans-serif",
        "::placeholder": {
            color: "#6b7280",
        },
    },
    invalid: {
        color: "#ef4444",
    },
};

const CheckoutForm = ({ plan, clientSecret, onPaymentSuccess, bookingId, formData, setFormData }) => {
    const stripe = useStripe();
    const elements = useElements();
    const [error, setError] = useState(null);
    const [processing, setProcessing] = useState(false);

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!stripe || !elements) return;

        setProcessing(true);
        setError(null);

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: elements.getElement(CardNumberElement),
                billing_details: {
                    name: formData.name,
                    email: formData.email,
                },
            },
        });

        if (error) {
            setError(error.message);
            setProcessing(false);
        } else if (paymentIntent.status === "succeeded") {
            try {
                const response = await axios.post("/api/payments/verify", {
                    booking_id: bookingId,
                    transaction_id: paymentIntent.id,
                    amount: plan.price,
                    payment_method: "Stripe",
                    status: "paid",
                });

                if (response.data.success) {
                    onPaymentSuccess();
                } else {
                    setError("Payment verified but update failed. Please contact support.");
                }
            } catch (err) {
                setError("Payment successful but verification failed.");
            }
            setProcessing(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-5">
                {/* Full Name */}
                <div>
                    <label className="block text-sm font-medium text-gray-400 mb-2">Full Name</label>
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <User size={16} className="text-purple-500/70 group-focus-within:text-purple-500 transition-colors" />
                        </div>
                        <input
                            type="text"
                            required
                            placeholder="John Smith"
                            value={formData.name}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            className="w-full bg-[#1C1C1E] border border-[#2C2C2E] rounded-xl pl-11 pr-4 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600/30 focus:border-purple-600/50 transition-all"
                        />
                    </div>
                </div>

                {/* Email Address */}
                <div>
                    <label className="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Mail size={16} className="text-purple-500/70 group-focus-within:text-purple-500 transition-colors" />
                        </div>
                        <input
                            type="email"
                            required
                            placeholder="john@company.com"
                            value={formData.email}
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                            className="w-full bg-[#1C1C1E] border border-[#2C2C2E] rounded-xl pl-11 pr-4 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600/30 focus:border-purple-600/50 transition-all"
                        />
                    </div>
                </div>

                {/* Card Number */}
                <div>
                    <label className="block text-sm font-medium text-gray-400 mb-2">Card Number</label>
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <CreditCard size={16} className="text-purple-500/70 group-focus-within:text-purple-500 transition-colors" />
                        </div>
                        <div className="w-full bg-[#1C1C1E] border border-[#2C2C2E] rounded-xl pl-11 pr-4 py-4 focus-within:ring-2 focus-within:ring-purple-600/30 focus-within:border-purple-600/50 transition-all">
                            <CardNumberElement options={{ style: stripeStyles, placeholder: "4242 4242 4242 4242" }} />
                        </div>
                    </div>
                </div>

                {/* Expiry and CVC */}
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-400 mb-2">Expiry Date</label>
                        <div className="w-full bg-[#1C1C1E] border border-[#2C2C2E] rounded-xl px-4 py-4 focus-within:ring-2 focus-within:ring-purple-600/30 focus-within:border-purple-600/50 transition-all">
                            <CardExpiryElement options={{ style: stripeStyles }} />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-400 mb-2">CVC</label>
                        <div className="w-full bg-[#1C1C1E] border border-[#2C2C2E] rounded-xl px-4 py-4 focus-within:ring-2 focus-within:ring-purple-600/30 focus-within:border-purple-600/50 transition-all">
                            <CardCvcElement options={{ style: stripeStyles, placeholder: "123" }} />
                        </div>
                    </div>
                </div>
            </div>

            {error && (
                <div className="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    {error}
                </div>
            )}

            <div className="flex items-center justify-center gap-8 text-[11px] text-gray-500 pt-2 uppercase tracking-widest font-bold">
                <div className="flex items-center gap-2">
                    <ShieldCheck size={14} className="text-gray-600" />
                    SSL Secured
                </div>
                <div className="flex items-center gap-2">
                    <Lock size={12} className="text-gray-600" />
                    256-Bit Encryption
                </div>
            </div>
        </form>
    );
};

const SuccessState = ({ plan }) => (
    <motion.div
        initial={{ scale: 0.9, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        className="bg-[#121214] border border-[#1F1F23] p-12 rounded-[2rem] max-w-lg w-full text-center space-y-8 shadow-2xl"
    >
        <div className="w-24 h-24 bg-purple-600/10 rounded-full flex items-center justify-center mx-auto border border-purple-600/20">
            <CheckCircle2 size={48} className="text-purple-500 text-shadow-glow" />
        </div>
        <div className="space-y-2">
            <h1 className="text-4xl font-extrabold text-white tracking-tight">Payment Received!</h1>
            <p className="text-gray-400 text-lg">
                Thank you! Your subscription to <b>{plan.name}</b> is now active.
            </p>
        </div>
        <button
            onClick={() => (window.location.href = "/dashboard")}
            className="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl shadow-lg transition-all text-lg"
        >
            Enter Dashboard
        </button>
    </motion.div>
);

export default function Checkout({ plan, stripeKey }) {
    const { auth } = usePage().props;
    const [stripePromise] = useState(() => loadStripe(stripeKey));
    const [clientSecret, setClientSecret] = useState("");
    const [booking, setBooking] = useState(null);
    const [isSuccess, setIsSuccess] = useState(false);
    const [loading, setLoading] = useState(true);

    // Initial state for form data using auth user if available
    const [formData, setFormData] = useState({
        name: auth.user?.name || "",
        email: auth.user?.email || "",
    });

    useEffect(() => {
        const initCheckout = async () => {
            try {
                const response = await axios.post("/api/bookings/create", {
                    pricing_plan_id: plan.id,
                });
                if (response.data.success) {
                    setClientSecret(response.data.data.client_secret);
                    setBooking(response.data.data.booking);
                }
            } catch (err) {
                console.error("Checkout init failed", err);
            } finally {
                setLoading(false);
            }
        };
        initCheckout();
    }, [plan.id]);

    return (
        <div className="min-h-screen bg-[#09090B] flex items-center justify-center p-6 relative overflow-hidden">
            <Head title="Checkout" />
            
            {/* Background Glows */}
            <div className="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-purple-900/10 rounded-full blur-[120px]"></div>
            <div className="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-indigo-900/10 rounded-full blur-[120px]"></div>

            <div className="w-full max-w-6xl relative z-10">
                {isSuccess ? (
                    <div className="flex justify-center"><SuccessState plan={plan} /></div>
                ) : loading ? (
                    <div className="flex flex-col items-center justify-center py-20 gap-4 text-gray-500">
                        <Loader2 className="animate-spin text-purple-600" size={48} />
                        <p className="font-medium animate-pulse tracking-wide">SECURE SYNC INITIALIZING...</p>
                    </div>
                ) : (
                    <div className="grid lg:grid-cols-12 gap-8 items-stretch">
                        
                        {/* Left: Payment Box */}
                        <motion.div
                            initial={{ opacity: 0, x: -50 }}
                            animate={{ opacity: 1, x: 0 }}
                            className="lg:col-span-7 bg-[#121214] border border-[#1F1F23] rounded-[2.5rem] p-10 lg:p-14 shadow-2xl flex flex-col justify-between"
                        >
                            <div>
                                <h1 className="text-3xl font-bold text-white mb-10 tracking-tight flex items-center gap-4">
                                    Payment Details
                                </h1>
                                <Elements
                                    stripe={stripePromise}
                                    options={{ clientSecret, appearance: { theme: 'night' } }}
                                >
                                    <CheckoutForm
                                        plan={plan}
                                        clientSecret={clientSecret}
                                        bookingId={booking?.id}
                                        formData={formData}
                                        setFormData={setFormData}
                                        onPaymentSuccess={() => setIsSuccess(true)}
                                    />
                                </Elements>
                            </div>
                        </motion.div>

                        {/* Right: Summary & Features */}
                        <div className="lg:col-span-5 flex flex-col gap-6">
                            
                            {/* Order Summary */}
                            <motion.div
                                initial={{ opacity: 0, x: 50 }}
                                animate={{ opacity: 1, x: 0 }}
                                className="bg-[#121214] border border-[#1F1F23] rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden"
                            >
                                <h2 className="text-2xl font-bold mb-8 text-white tracking-tight">Order Summary</h2>
                                
                                <div className="space-y-5 relative z-10">
                                    <div className="flex justify-between text-gray-400 font-medium">
                                        <span>Subtotal</span>
                                        <span className="text-white">${(plan.price * 1.2).toLocaleString()}</span>
                                    </div>
                                    <div className="flex justify-between text-gray-400 font-medium">
                                        <span>Setup Discount</span>
                                        <span className="text-emerald-500">-${(plan.price * 0.2).toLocaleString()}</span>
                                    </div>
                                    <div className="h-px bg-gradient-to-r from-transparent via-[#1F1F23] to-transparent my-4"></div>
                                    <div className="flex justify-between items-end">
                                        <span className="text-gray-400 text-lg font-bold">Total Due Today</span>
                                        <span className="text-4xl font-black text-white tracking-tighter">${plan.price.toLocaleString()}</span>
                                    </div>

                                    <button
                                        form="payment-form" // This would require the submit button to be outside, or simplified
                                        onClick={() => document.querySelector('form').requestSubmit()}
                                        className="w-full mt-6 bg-gradient-to-r from-[#A855F7] to-[#6366F1] hover:shadow-[0_0_30px_rgba(168,85,247,0.4)] text-white font-black py-4 rounded-2xl transition-all text-lg uppercase tracking-widest"
                                    >
                                        Complete Payment
                                    </button>
                                    
                                    <p className="text-center text-[10px] text-gray-600 mt-6 leading-relaxed font-bold uppercase tracking-wider">
                                        By Completing This Purchase, You Agree To Our <span className="text-purple-500/50 hover:text-purple-500 cursor-pointer">Terms Of Service</span>
                                    </p>
                                </div>
                                <div className="absolute top-0 right-0 w-32 h-32 bg-purple-600/5 blur-3xl rounded-full"></div>
                            </motion.div>

                            {/* Features Box */}
                            <motion.div
                                initial={{ opacity: 0, x: 50 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ delay: 0.1 }}
                                className="bg-[#121214] border border-[#1F1F23] rounded-[2.5rem] p-10 shadow-xl flex-1"
                            >
                                <h2 className="text-xl font-bold mb-8 text-white tracking-tight uppercase">Order What's Included</h2>
                                <ul className="space-y-6">
                                    <li className="flex justify-between items-center group">
                                        <div className="flex items-center gap-4">
                                            <div className="p-2 bg-purple-600/10 rounded-full border border-purple-600/20 group-hover:bg-purple-600/20 transition-all">
                                                <CheckCircle2 size={16} className="text-purple-500" />
                                            </div>
                                            <span className="text-gray-300 font-medium text-sm">Immediate Access To Dashboard</span>
                                        </div>
                                        <span className="text-white/40 font-bold text-sm">$1,500</span>
                                    </li>
                                    <li className="flex justify-between items-center group">
                                        <div className="flex items-center gap-4">
                                            <div className="p-2 bg-purple-600/10 rounded-full border border-purple-600/20 group-hover:bg-purple-600/20 transition-all">
                                                <CheckCircle2 size={16} className="text-purple-500" />
                                            </div>
                                            <span className="text-gray-300 font-medium text-sm">Kickoff Call Within 24 Hours</span>
                                        </div>
                                        <span className="text-white/40 font-bold text-sm">$2,500</span>
                                    </li>
                                    <li className="flex justify-between items-center group">
                                        <div className="flex items-center gap-4">
                                            <div className="p-2 bg-purple-600/10 rounded-full border border-purple-600/20 group-hover:bg-purple-600/20 transition-all">
                                                <CheckCircle2 size={16} className="text-purple-500" />
                                            </div>
                                            <span className="text-gray-300 font-medium text-sm">First Month - Growth & Authority</span>
                                        </div>
                                        <span className="text-white/40 font-bold text-sm">$3,000</span>
                                    </li>
                                </ul>
                            </motion.div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
