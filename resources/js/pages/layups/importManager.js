import helper from "../../helpers/helper";
import conflictManager from "./conflictManager";

export default function importManager(id = null) {
    return {
        supplierId: id,
        previewData: null,
        decisions: null,
        fileUrl: null,

        conflicts: [],
        allPreviewData: [],
        isCheckedConflict: false,
        //
        showWarning: false,
        warningMessage: "",
        onConfirm: null,

        fileKey: 0,
        dryRun: false,

        finishCheck: false,

        results: {},

        action: "skip",

        errors: {},

        init() {
            window.addEventListener("submit-conflict", (e) => {
                this.results = e.detail.results;
                this.finishCheck = e.detail.finish;

                console.log("Dari modal:", this.results);
                console.log("Dari finish:", e.detail);
                if (e.detail.finish) {
                    this.conflicts = [];
                }

                // this.confirmImport();
            });

            // lanjut import
            // this.confirmImport(results);
        },

        openModalConflict() {
            window.dispatchEvent(
                new CustomEvent("open-conflict", {
                    detail: this.allPreviewData,
                }),
            );
        },

        // async handleFile(e) {
        //     const file = e.target.files[0];

        //     console.log(this.action);

        //     console.log(this.supplierId);
        //     const formData = new FormData();
        //     formData.append("file", file);
        //     formData.append("supplier_id", this.supplierId);

        //     // const res = await axios.post(
        //     //     "/api/layups/import/preview",
        //     //     formData,
        //     // );
        //     await this.preview(formData);

        //     this.allPreviewData = res.data;
        //     this.previewData = res.data.data;
        //     this.conflicts = res.data.conflicts;
        //     this.isCheckedConflict = true;
        //     if (file) {
        //         this.fileUrl = URL.createObjectURL(file);
        //     }

        //     console.log(res.data);
        // },

        async handleFile(e) {
            const file = e.target.files[0];

            if (!file) return;

            const formData = new FormData();
            formData.append("file", file);
            formData.append("supplier_id", this.supplierId);

            try {
                const res = await this.preview(formData);

                this.allPreviewData = res;
                this.previewData = res.data;
                this.conflicts = res.conflicts;
                this.isCheckedConflict = true;

                this.fileUrl = URL.createObjectURL(file);

                console.log(res);
            } catch (error) {
                const message =
                    error.response?.data?.message || "Upload failed";

                helper().showToast("error", message);
            }
        },

        async preview(formData) {
            const res = await axios.post(
                "/api/layups/import/preview",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                },
            );

            return res.data;
        },

        openModalWarning(message, onConfirm) {
            this.warningMessage = message;
            this.onConfirm = onConfirm;
            helper().openModal("modal-confirm");
        },

        closeModalWarning() {
            this.showWarning = false;
            helper().closeModal("modal-confirm");
            this.warningMessage = "";
            this.onConfirm = null;
        },

        confirmWarning() {
            if (this.onConfirm) {
                this.confirmImport();
            }
            this.closeModalWarning();
        },

        isManualAction() {
            return ["overwrite", "duplicate"].includes(this.action);
        },

        preImport() {
            if (this.dryRun) {
                this.action = "dry";
                this.forceSubmit();
                return;
            }
            const hasConflict = this.conflicts?.length > 0;

            if (this.action === "reject" && hasConflict && !this.finishCheck) {
                this.openModalConflict();
                return;
            }
            if (hasConflict && this.isManualAction()) {
                // alert(this.action);
                this.openModalWarning(
                    `There are still unresolved conflicts. Are you sure to continue?`,
                    () => {
                        // this.forceSubmit();
                    },
                );
                return;
            }

            this.forceSubmit();
        },

        async forceSubmit() {
            await this.confirmImport();
            // helper().closeModal("modal-confirm");
        },

        resetFile() {
            this.previewData = null;
            this.allPreviewData = [];
            this.conflicts = [];
            this.decisions = null;
            this.fileUrl = null;
            this.isCheckedConflict = false;
            this.finishCheck = false;
            this.action = "skip";
            this.dryRun = false;

            // this.$refs.fileInput.value = null;
            this.fileKey++;
        },

        cancelImport() {
            this.previewData = null;
            this.allPreviewData = [];
            this.conflicts = [];
            this.decisions = null;
            this.fileUrl = null;
            this.isCheckedConflict = false;
            this.finishCheck = false;

            this.$refs.fileInput.value = null;

            helper().closeModal("modal-upload");
        },

        submitImport() {
            this.preImport();
        },

        async confirmImport() {
            try {
                await axios.post("/api/layups/import", {
                    data: this.previewData,
                    decisions: this.decisions,
                    action: this.action,
                    newData: this.results,
                });

                // this.closeImport();
                this.resetFile();
                helper().closeModal("modal-upload");

                helper().showToast("success", "Import success!");

                this.$nextTick(() => {
                    window.location.reload();
                });
            } catch (error) {
                console.log(error);
                helper().showToast("error", error?.data?.data?.message);
            } finally {
            }
        },
    };
}
