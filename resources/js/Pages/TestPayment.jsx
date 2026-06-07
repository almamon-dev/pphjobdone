import React, { useState } from "react";
import { loadStripe } from "@stripe/stripe-js";
import { Elements, PaymentElement, useStripe, useElements } from "@stripe/react-stripe-js";
import { Head, usePage } from "@inertiajs/react";

// Replace with actual Stripe Test Publishable Key later if needed, but we can pass it from backend.
// For now, it's fine to hardcode the dummy key for UI testing or get it from env if configured.
const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_KEY || "pk_test_51Q12svRvN5gEyL4x6R3RSVVlyqPi00XkuQy1zDAYXvKjmtOZ1eVouTlCiwE3GrdkJQLywLK13o3WbE3Ztno6HEq100BwpAY1ZV");

const CheckoutForm = ({ clientSecret, bookingId, token }) => {
    const stripe = useStripe();
    const elements = useElements();
    const [message, setMessage] = useState(null);
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!stripe || !elements) return;

        setIsLoading(true);

        const { error, paymentIntent } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                // Return URL isn't strictly used if redirect="if_required" for cards, but required by API
                return_url: window.location.origin + "/dashboard",
            },
            redirect: "if_required"
        });

        if (error) {
            setMessage(error.message);
        } else if (paymentIntent && paymentIntent.status === "succeeded") {
            setMessage("Payment Successful on Stripe! (Remaining 'pending' in your DB because Webhook is not connected locally)");
        }
        setIsLoading(false);
    };

    return (
        <form onSubmit={handleSubmit} className="mt-4">
            <PaymentElement />
            <button
                disabled={isLoading || !stripe || !elements}
                className="mt-6 w-full rounded-md bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700 disabled:opacity-50 transition"
            >
                {isLoading ? "Processing..." : "Pay Now"}
            </button>
            {message && <div className="mt-4 text-center text-red-500 font-semibold">{message}</div>}
        </form>
    );
};

export default function TestPayment({ auth, token }) {
    const [clientSecret, setClientSecret] = useState("");
    const [bookingId, setBookingId] = useState(null);
    const [loading, setLoading] = useState(false);

    // Make sure we have the token
    // In Inertia, auth is typically managed by session, but since our API is Sanctum, 
    // it will work automatically if called within the same domain due to Sanctum's SPA cookie auth.
    // If we call the API endpoint from Inertia frontend using axios, we don't even need Bearer token.
    
    const handleCreateBooking = async () => {
        setLoading(true);
        try {
            const response = await window.axios.post("/api/campaign-bookings/create", {
                campaign_tier_id: 1, // Assumes a campaign tier with ID 1 exists
                campaign_details: {
                    links: "https://test.com",
                    keywords: "test payment",
                },
            }, {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });

            if (response.data?.data?.client_secret) {
                setClientSecret(response.data.data.client_secret);
                setBookingId(response.data.data.booking.id);
            } else {
                alert("Failed to get client_secret. Check API response.");
            }
        } catch (error) {
            console.error("Error creating booking:", error);
            alert("Error: " + (error.response?.data?.message || error.message));
        }
        setLoading(false);
    };

    return (
        <div className="min-h-screen bg-gray-100 flex flex-col items-center py-12 px-4 sm:px-6 lg:px-8">
            <Head title="Test Payment" />
            
            <div className="max-w-md w-full bg-white rounded-lg shadow p-8">
                <h2 className="text-2xl font-bold text-center text-gray-900 mb-8">Stripe Payment Test</h2>
                
                {!clientSecret ? (
                    <div className="text-center">
                        <p className="text-gray-600 mb-6 text-sm">
                            Click the button below to hit your `campaign-bookings/create` API, generate a new booking, and get the Client Secret.
                        </p>
                        <button
                            onClick={handleCreateBooking}
                            disabled={loading}
                            className="w-full bg-emerald-600 text-white rounded-md py-3 font-semibold hover:bg-emerald-700 disabled:opacity-50 transition"
                        >
                            {loading ? "Calling API..." : "1. Create Booking & Get Secret"}
                        </button>
                    </div>
                ) : (
                    <div>
                        <div className="mb-4 p-3 bg-green-50 text-green-700 border border-green-200 rounded text-sm">
                            <strong>Success!</strong> Client Secret received. You can now test the payment using Stripe dummy cards (e.g. 4242 4242 4242 4242).
                        </div>
                        <Elements stripe={stripePromise} options={{ clientSecret }}>
                            <CheckoutForm clientSecret={clientSecret} bookingId={bookingId} token={token} />
                        </Elements>
                    </div>
                )}
            </div>
        </div>
    );
}
