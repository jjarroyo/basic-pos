import Chart from "chart.js/auto";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

// Make Chart available globally
window.Chart = Chart;

// Configure Laravel Echo for Reverb with dynamic IP
window.Pusher = Pusher;

// Get server configuration dynamically
async function getReverbConfig() {
    try {
        // Try to get config from API
        const response = await fetch("/api/reverb-config");
        if (response.ok) {
            const config = await response.json();
            return config;
        }
    } catch (e) {
        console.warn("Could not fetch Reverb config from API, using defaults");
    }

    // Fallback to environment variables
    return {
        key: import.meta.env.VITE_REVERB_APP_KEY || "default-key",
        host: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        port: import.meta.env.VITE_REVERB_PORT || 8080,
        scheme: import.meta.env.VITE_REVERB_SCHEME || "http",
    };
}

// Initialize Echo with dynamic config
getReverbConfig()
    .then((config) => {
        window.Echo = new Echo({
            broadcaster: "reverb",
            key: config.key,
            wsHost: config.host,
            wsPort: config.port,
            wssPort: config.port,
            forceTLS: config.scheme === "https",
            enabledTransports: ["ws", "wss"],
        });

        console.log(
            `🔌 Connecting to Reverb at ${config.scheme}://${config.host}:${config.port}`
        );

        // Listen for stock updates in real-time
        window.Echo.channel("stock-updates").listen(
            "ProductStockUpdated",
            (e) => {
                console.log("📦 Stock actualizado en tiempo real:", e);

                // Dispatch Livewire event to update UI
                if (window.Livewire) {
                    window.Livewire.dispatch("stock-updated", {
                        productId: e.product_id,
                        newStock: e.new_stock,
                        updatedBy: e.updated_by,
                    });
                }

                // Show toast notification
                if (window.showToast) {
                    window.showToast(
                        `Stock actualizado: Producto #${e.product_id} ahora tiene ${e.new_stock} unidades`,
                        "info"
                    );
                }
            }
        );

        // Listen for cash register status updates
        window.Echo.channel("cash-registers").listen(
            "CashRegisterStatusUpdated",
            (e) => {
                console.log("💰 Estado de caja actualizado:", e);

                // Dispatch Livewire event to update UI
                if (window.Livewire) {
                    window.Livewire.dispatch("cash-register-updated", {
                        cashRegisterId: e.cash_register_id,
                        isOpen: e.is_open,
                        userId: e.user_id,
                        sessionId: e.session_id,
                    });
                }

                // Show toast notification
                const status = e.is_open ? "abierta" : "cerrada";
                if (window.showToast) {
                    window.showToast(
                        `Caja #${e.cash_register_id} ${status}`,
                        "info"
                    );
                }
            }
        );

        console.log(
            "✅ Laravel Echo configurado y escuchando eventos en tiempo real"
        );
    })
    .catch((error) => {
        console.error("❌ Error configurando Laravel Echo:", error);
    });
