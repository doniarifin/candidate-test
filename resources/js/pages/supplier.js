import helper from "../helpers/helper";
const h = helper();

export default function supplierPage(id = null) {
    return {
        suppliers: {
            layups: [],
        },
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // details
        supplierId: id,
        supplier: null,

        editData: {},
        deleteId: null,
        deleteName: "",

        selectedIds: [],

        //
        search: "",

        loading: false,

        form: {
            name: "",
            code: "",
            email: null,
            location: null,
            certifications: null,
            status: null ?? "active",
        },

        //layup data
        layup: {
            supplier_id: "",
            name: "",
            code: "",
            grade: "",
            revision: "",
            status: null ?? "draft",
        },

        errors: {},

        init() {
            if (this.supplierId) {
                this.getDataById();
            } else {
                this.getData();
            }
        },

        openDeleteModal(id, name) {
            h.openModal("delete-modal");
            this.deleteId = id;
            this.deleteName = name;
            this.openDelete = true;
        },

        closeDeleteModal() {
            this.openDelete = false;
        },

        closeModal(name) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: name }),
            );
        },

        openModal(name) {
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: name }),
            );
        },

        openEditModal(name, data) {
            console.log(this.layup);
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: name }),
            );
        },

        async getData() {
            this.loading = true;
            // this.suppliers = [];
            try {
                const response = await axios.get("/api/suppliers");
                this.suppliers = response.data.data;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async getDataById() {
            this.loading = true;

            try {
                const res = await axios.get(
                    `/api/suppliers/${this.supplierId}`,
                );
                // const data = await res.json();

                this.supplier = res.data;
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        async submitCreate() {
            this.loading = true;
            this.errors = {};

            try {
                const res = await axios.post("/api/suppliers", this.form);

                this.form.name = "";
                this.form.code = "";
                this.form.email = "";
                this.form.location = "";
                this.form.certifications = "";
                this.form.status = "";

                // this.openCreate = false;
                h.closeModal("add-supplier");

                await this.getData();

                // console.log(response.data.data);

                window.dispatchEvent(
                    new CustomEvent("toast", {
                        detail: {
                            type: "success",
                            message: "Supplier success created!",
                        },
                    }),
                );
            } catch (error) {
                // if (error.response.status === 422) {
                this.errors = error.response?.data?.errors;
                // }
            } finally {
                this.loading = false;
            }
        },

        async updateData() {
            this.loading = true;
            this.errors = {};

            try {
                const res = await axios.put(
                    `/api/suppliers/${this.supplierId}`,
                    this.editData,
                );

                this.editData.name = "";
                this.editData.code = "";
                this.editData.email = "";
                this.editData.location = "";
                this.editData.certifications = "";
                this.editData.status = "";

                // this.openEdit = false;
                h.closeModal("edit-supplier");

                await this.getDataById();
                // window.location.reload();

                window.dispatchEvent(
                    new CustomEvent("toast", {
                        detail: {
                            type: "success",
                            message: "Supplier updated!",
                        },
                    }),
                );
            } catch (error) {
                // if (error.response.status === 422) {
                this.errors = error.response?.data?.errors;
                // }
            } finally {
                this.loading = false;
            }
        },

        async exportSuppliers(ids) {
            if (!ids?.length) {
                h.showToast("error", "Please select at least one data!");
                return;
            }
            try {
                const res = await axios.post("/api/suppliers/export", {
                    ids: ids,
                });

                const dataStr = JSON.stringify(res.data, null, 2);

                const blob = new Blob([dataStr], { type: "application/json" });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement("a");
                a.href = url;
                a.download = `suppliers-export.json`;
                a.click();
                h.showToast("success", "Export success!");
            } catch (err) {
                this.errors = err.response?.data?.message;
                // console.log(err.response);
                h.showToast("error", this.errors);
                console.error(err);
            }
        },

        async exportLayups(ids) {
            if (!ids?.length) {
                h.showToast("error", "Please select at least one data!");
                return;
            }
            try {
                const res = await axios.post("/api/layups/export", {
                    ids: ids,
                });

                const dataStr = JSON.stringify(res.data, null, 2);

                const blob = new Blob([dataStr], { type: "application/json" });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement("a");
                a.href = url;
                a.download = `suppliers-export.json`;
                a.click();
                h.showToast("success", "Export success!");
            } catch (err) {
                this.errors = err.response?.data?.message;
                // console.log(err.response);
                h.showToast("error", this.errors);
                console.error(err);
            }
        },

        async createLayup() {
            this.loading = true;
            this.errors = {};

            this.layup.supplier_id = this.supplierId;

            try {
                const res = await axios.post("/api/layups", this.layup);

                this.layup.name = "";
                this.layup.code = "";
                this.layup.grade = "";
                this.layup.revision = "";
                this.layup.status = "";

                h.closeModal("add-layup");

                await this.getDataById();

                h.showToast("success", "Layup created success!");
            } catch (error) {
                this.errors = error.response?.data?.errors;
                h.showToast("error", error.response?.data?.message);
            } finally {
                this.loading = false;
            }
        },

        async updateLayup() {
            this.loading = true;
            this.errors = {};

            try {
                const res = await axios.put(
                    `/api/layups/${this.editData?.id}`,
                    this.editData,
                );

                this.editData.name = "";
                this.editData.supplier_id = "";
                this.editData.code = "";
                this.editData.grade = "";
                this.editData.revision = "";
                this.editData.status = "";

                // this.openEdit = false;
                h.closeModal("edit-layup");

                await this.getDataById();
                // window.location.reload();

                window.dispatchEvent(
                    new CustomEvent("toast", {
                        detail: {
                            type: "success",
                            message: "Layups updated!",
                        },
                    }),
                );
            } catch (error) {
                // if (error.response.status === 422) {
                this.errors = error.response?.data?.errors;
                // }
            } finally {
                this.loading = false;
            }
        },

        async deleteLayup() {
            this.loading = true;
            this.errors = {};

            try {
                await axios.delete(`/api/layups/${this.deleteId}`);

                // this.openEdit = false;
                h.closeModal("delete-modal");

                await this.getDataById();
                // window.location.reload();

                window.dispatchEvent(
                    new CustomEvent("toast", {
                        detail: {
                            type: "success",
                            message: "Deleted success!",
                        },
                    }),
                );
            } catch (error) {
                // if (error.response.status === 422) {
                this.errors = error.response?.data?.errors;
                // }
            } finally {
                this.loading = false;
            }
        },
    };
}
