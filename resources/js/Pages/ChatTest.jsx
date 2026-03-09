import React, { useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';

export default function ChatTest() {
    const { auth } = usePage().props;
    const [messages, setMessages] = useState([]);
    const [status, setStatus] = useState('Connecting...');
    const [selectedConversation, setSelectedConversation] = useState(null);

    useEffect(() => {
        if (!auth.user) {
            setStatus('Error: Please login first to test private channels.');
            return;
        }

        setStatus(`Connected as ${auth.user.name}. Waiting for messages...`);

        // Listen on Private User Channel (Notifications)
        const userChannel = window.Echo.private(`user.${auth.user.id}`);
        userChannel.listen('.message.sent', (data) => {
            console.log('Private User Channel: New message:', data);
            setMessages(prev => [data.message, ...prev]);
            setStatus(`New private message received at ${new Date().toLocaleTimeString()}`);
        });

        // Cleanup
        return () => {
            window.Echo.leave(`user.${auth.user.id}`);
        };
    }, [auth.user]);

    return (
        <div style={{ padding: '40px', fontFamily: 'Inter, sans-serif', backgroundColor: '#09090b', color: '#fff', minHeight: '100vh' }}>
            <Head title="Chat Private Test" />
            
            <div style={{ maxWidth: '800px', margin: '0 auto' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '24px' }}>
                    <div>
                        <h1 style={{ fontSize: '24px', fontWeight: 'bold', marginBottom: '4px' }}>Chat Real-time (Private)</h1>
                        <p style={{ color: '#a1a1aa', fontSize: '14px' }}>{status}</p>
                    </div>
                    {auth.user && (
                        <div style={{ textAlign: 'right', background: '#27272a', padding: '8px 16px', borderRadius: '20px', fontSize: '14px' }}>
                            Auth ID: <span style={{ color: '#bb86fc' }}>{auth.user.id}</span>
                        </div>
                    )}
                </div>

                {!auth.user && (
                    <div style={{ background: '#450a0a', border: '1px solid #7f1d1d', color: '#fca5a5', padding: '12px', borderRadius: '8px', marginBottom: '20px' }}>
                        Warning: Authentication detected thake na. Private channel check korar jonno login kora thakte hobe.
                    </div>
                )}

                <div style={{ 
                    border: '1px solid #27272a', 
                    borderRadius: '12px', 
                    padding: '20px', 
                    backgroundColor: '#18181b',
                    minHeight: '400px',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '12px',
                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
                }}>
                    <div style={{ color: '#71717a', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '0.05em', borderBottom: '1px solid #27272a', paddingBottom: '8px' }}>
                        Live Message Feed
                    </div>
                    
                    {messages.length === 0 ? (
                        <div style={{ color: '#71717a', textAlign: 'center', marginTop: '100px' }}>
                            Waiting for broadcasts... Try hitting the test endpoint or Postman.
                        </div>
                    ) : (
                        messages.map((msg, index) => (
                            <div key={index} style={{ 
                                padding: '16px', 
                                backgroundColor: auth.user && msg.receiver_id === auth.user.id ? '#1e1b4b' : '#27272a',
                                borderRadius: '8px',
                                border: '1px solid #3f3f46',
                                animation: 'fadeIn 0.3s ease-out'
                            }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '8px' }}>
                                    <span style={{ fontSize: '12px', color: '#bb86fc', fontWeight: '600' }}>
                                        From User {msg.sender_id} → To User {msg.receiver_id}
                                    </span>
                                    <span style={{ fontSize: '10px', color: '#71717a' }}>
                                        Type: {msg.type}
                                    </span>
                                </div>
                                <div style={{ fontSize: '16px', lineHeight: '1.5' }}>{msg.message}</div>
                                <div style={{ fontSize: '10px', color: '#52525b', marginTop: '12px', display: 'flex', justifyContent: 'space-between' }}>
                                    <span>Conversation ID: {msg.conversation_id}</span>
                                    <span>{new Date(msg.created_at).toLocaleTimeString()}</span>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>

            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; transform: scale(0.95); }
                    to { opacity: 1; transform: scale(1); }
                }
            `}</style>
        </div>
    );
}
