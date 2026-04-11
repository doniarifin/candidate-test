import helpers from "../../helpers/helper";
import conflictManager from "./conflictManager";
const helper = helpers();

export default function layupManager(id = null) {
    return {
        layups: [],
        layers: [],

        revOrderLayers: [],

        showLayerModal: false,
        showDeleteModal: false,
        editingLayer: false,
        selectedLayer: null,

        editData: {},
        layerId: null,
        layupId: id,

        loading: false,
        totalThickness: 0,

        //layer from
        form: {
            layup_id: "",
            layer_order: 0,
            thickness: "",
            width: "",
            angle: 0,
            grade: "",
        },

        layupForm: {},

        errors: {},

        init() {
            if (this.layupId) {
                this.getDataLuById();
            } else {
                this.getDataLayup();
            }
        },

        async getDataLayup() {
            this.loading = true;
            this.layers = [];
            this.layups = [];

            try {
                const res = await axios.get("/api/layups");
                this.layups = res.data.data;
                this.layers = res.data.data.layers || [];
                this.revOrderLayers = res.data.data.layers || [];

                // console.log(this.layups);
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async getDataLuById() {
            this.loading = true;
            this.layers = [];
            this.layups = [];

            try {
                const res = await axios.get(`/api/layups/${this.layupId}`);

                this.layups = res.data;
                this.layers = res.data.layers || [];
                this.revOrderLayers = res.data.layers || [];
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        openAddModal() {
            this.editingLayer = false;
            console.log("editingLayer?", this.editingLayer);

            this.form = {
                id: null,
                layer_order: 1,
                thickness: 0,
                width: 0,
                angle: 0,
                grade: "",
            };
            // this.showLayerModal = true;
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: "modal-layer" }),
            );
        },

        editLayer(layer) {
            this.editingLayer = true;
            this.form = { ...layer };
            // this.showLayerModal = true;
            helper.openModal("modal-layer");
        },

        closeModal(name) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: name }),
            );
        },

        confirmDelete(layer) {
            this.selectedLayer = layer;
            // this.showDeleteModal = true;
            helper.openModal("delete-layer");
        },

        closeDeleteModal() {
            helper.closeModal("delete-layer");
            // this.showDeleteModal = false;
        },

        statusClass(status) {
            return {
                "bg-green-100 text-green-700": status === "active",
                "bg-yellow-100 text-yellow-700": status === "draft",
                "bg-gray-200 text-gray-600": status === "archived",
            };
        },

        statusLabel(status) {
            if (status === "active") return "Active";
            if (status === "draft") return "Draft";
            if (status === "archived") return "Archived";

            return "Unknown";
        },

        async saveLayup() {
            this.loading = true;
            this.errors = {};

            this.layups.layers = [...this.revOrderLayers];

            this.layupForm = this.layups;

            console.log(this.layupForm);
            // return;

            try {
                await axios.put(
                    `/api/layups/${this.layupForm.id}`,
                    this.layupForm,
                );

                this.layupForm = {};

                await this.getDataLuById();

                helper.showToast("success", "Update layup success!");
            } catch (error) {
                this.errors = error.response?.data?.errors;
                helper.showToast("error", error.response?.data?.message);
            } finally {
                this.loading = false;
            }
        },

        async preSubmitLayer() {
            if (this.editingLayer) return;

            if (!this.layers || this.layers.length === 0) {
                this.form.layer_order = 1;
                return;
            }

            const maxOrder = Math.max(
                0,
                ...this.layers.map((l) => Number(l.layer_order) || 0),
            );

            this.form.layer_order = maxOrder + 1;
        },

        async saveLayerData() {
            this.loading = true;
            this.errors = {};

            this.form.layup_id = this.layupId;

            if (!this.editingLayer) {
                await this.preSubmitLayer();
            }

            try {
                if (this.editingLayer) {
                    await axios.put(`/api/layers/${this.form.id}`, this.form);
                } else {
                    await axios.post("/api/layers", this.form);
                }

                this.form.layer_order = 0;
                this.form.thickness = 0;
                this.form.width = 0;
                this.form.angle = 0;
                this.form.grade = "";

                this.closeModal("modal-layer");

                await this.getDataLuById();

                helper.showToast(
                    "success",
                    this.editingLayer
                        ? "Update layer success!"
                        : "Create layer success!",
                );
            } catch (error) {
                this.errors = error.response?.data?.errors;
                helper.showToast("error", error.response?.data?.message);
            } finally {
                this.loading = false;
            }
        },

        async deleteLayer() {
            this.loading = true;
            this.errors = {};

            try {
                await axios.delete(`/api/layers/${this.selectedLayer.id}`);

                this.closeDeleteModal();

                await this.getDataLuById();

                helper.showToast("success", "Deleted success!");
            } catch (error) {
                this.errors = error.response?.data?.errors;
                helper.showToast("success", error.response?.data?.message);
            } finally {
                this.loading = false;
            }
        },

        async handleSort(item, position) {
            const layers = [...this.revOrderLayers];

            console.log("item", item);
            console.log("posisi", position);

            const oldIndex = layers.findIndex((l) => l.id === item.id);

            console.log("OLD:", oldIndex, "NEW:", position);

            if (oldIndex === -1) return;
            if (oldIndex === position) return;

            const [movedItem] = layers.splice(oldIndex, 1);

            layers.splice(position, 0, movedItem);

            layers.forEach((layer, index) => {
                layer.layer_order = index + 1;
            });

            console.log(layers);
            this.revOrderLayers = layers;
        },
    };
}
