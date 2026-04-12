import helper from "../../helpers/helper";

export default function conflictManager() {
    return {
        open: false,
        selected: 0,

        conflicts: [],
        decisions: {},
        selectedCId: null,

        showUnresolved: false,
        showResolved: false,

        //
        showWarning: false,
        warningMessage: "",
        onConfirmWarning: null,

        // resolved: {},

        init() {
            window.addEventListener("open-conflict", (e) => {
                this.openModal(e.detail);
            });
        },

        // get current() {
        //     return this.conflicts[this.selected] || {};
        // },
        get current() {
            return this.conflicts.find((c) => c.id === this.selectedCId) || {};
        },

        getNextUnresolved() {
            return this.conflicts.find((c) => !this.decisions[c.id]);
        },

        get unresolved() {
            return this.conflicts.filter((c) => !this.decisions[c.id]);
        },

        get resolved() {
            return this.conflicts.filter((c) => this.decisions[c.id]);
        },

        select(item) {
            this.selectedCId = item.id;
        },

        next() {
            let list = [];

            if (this.decisions[this.selectedCId]) {
                list = this.resolved;
            } else {
                list = this.unresolved;
            }

            if (!list.length) return;

            const currentIndex = list.findIndex(
                (c) => c.id === this.selectedCId,
            );

            if (currentIndex < list.length - 1) {
                this.selectedCId = list[currentIndex + 1].id;
            } else {
                this.selectedCId = list[0].id;
            }
        },

        goNext() {
            const remaining = this.unresolved;

            if (remaining.length > 0) {
                this.selectedCId = remaining[0].id;
            } else {
                this.selectedCId = null;

                console.log("All resolved");
            }
        },

        openModal(previewData) {
            this.selected = 0;
            this.decisions = {};
            this.conflicts = previewData.conflicts ?? [];

            helper().openModal("modal-conflict");
        },

        keepExisting() {
            const id = this.current.id;
            this.decisions[id] = "keep_existing";
            this.goNext();
        },

        acceptNew() {
            const id = this.current.id;
            this.decisions[id] = "accept_new";
            this.goNext();
        },

        isDifferent(index, row) {
            const existing = this.current.existing[index];
            if (!existing) return false;

            return (
                existing.thickness !== row.thickness ||
                existing.width !== row.width ||
                existing.angle !== row.angle
            );
        },

        closeModalConflict() {
            this.openModalWarning(
                `Leaving will discard your selections. Are you sure to continue?`,
                () => {
                    this.forceClose();
                },
            );
        },

        openModalWarning(message, onConfirm) {
            this.warningMessage = message;
            this.onConfirmWarning = onConfirm;
            this.showWarning = true;
            helper().openModal("modal-warning");
        },

        confirmWarning() {
            if (this.onConfirmWarning) {
                this.onConfirmWarning();
            }
            this.closeModalWarning();
        },

        closeModalWarning() {
            this.showWarning = false;
            helper().closeModal("modal-warning");
            this.warningMessage = "";
            this.onConfirmWarning = null;
        },

        forceClose() {
            const results = this.conflicts.map((conflict) => {
                const decision = this.decisions[conflict.id];

                let selectedData = null;

                if (decision === "accept_new") {
                    selectedData = conflict.importing;
                } else if (decision === "keep_existing") {
                    selectedData = conflict.existing;
                }

                return {
                    id: conflict.id,
                    name: conflict.name,
                    code: conflict.code,
                    supplier_id: conflict.supplier_id,
                    finish: false,
                    checked: true,
                    decision,
                    layers: selectedData,
                };
            });

            window.dispatchEvent(
                new CustomEvent("submit-conflict", {
                    detail: {
                        results: results,
                        finish: false,
                    },
                }),
            );

            helper().closeModal("modal-conflict");
        },

        finish() {
            const results = this.conflicts.map((conflict) => {
                const decision = this.decisions[conflict.id];

                let selectedData = null;

                if (decision === "accept_new") {
                    selectedData = conflict.importing;
                } else if (decision === "keep_existing") {
                    selectedData = conflict.existing;
                }

                const layersWithLayupId = selectedData.map((layer) => ({
                    ...layer,
                    layup_id: conflict.id,
                }));

                return {
                    id: conflict.id,
                    name: conflict.name,
                    code: conflict.code,
                    supplier_id: conflict.supplier_id,
                    finish: true,
                    checked: true,
                    decision,
                    layers: layersWithLayupId,
                };
            });

            console.log("Final result:", results);

            window.dispatchEvent(
                new CustomEvent("submit-conflict", {
                    detail: {
                        results: results,
                        finish: true,
                    },
                }),
            );

            helper().closeModal("modal-conflict");
        },
    };
}
