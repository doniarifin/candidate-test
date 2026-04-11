export default function helper() {
    return {
        formatDate(date) {
            if (!date) return "-";

            const d = new Date(date);

            return d.toLocaleDateString("id-ID", {
                year: "numeric",
                month: "short",
                day: "2-digit",
            });
        },

        openModal(name) {
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: name }),
            );
        },
        closeModal(name) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: name }),
            );
        },

        showToast(type, message) {
            window.dispatchEvent(
                new CustomEvent("toast", {
                    detail: {
                        type: type,
                        message: message,
                    },
                }),
            );
        },
    };
}
