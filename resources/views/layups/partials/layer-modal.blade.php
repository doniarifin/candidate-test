<x-modal x-data="layerManager" name="modal-layer" maxWidth="md" :closeable="false">
    <!-- <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6"> -->

        <!-- header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">
                <span x-text="editingLayer ? 'Edit Layer' : 'Add Layer'"></span>
            </h2>

            <button 
                type="button" 
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- form -->
        <form @submit.prevent="saveLayerData" class="flex flex-col max-h-[70vh]">
        <!-- body -->
            <div class="p-6 space-y-4 overflow-y-auto">
            <!-- <div class="space-y-4"> -->

                <x-input
                    required="true"
                    class="mb-4"
                    label="Thickness (mm)"
                    name="thickness"
                    model="form.thickness"
                />
                <x-input
                    required="true"
                    class="mb-4"
                    label="Width (mm)"
                    name="width"
                    model="form.width"
                />
                <x-input
                    required="true"
                    class="mb-4"
                    label="Angle in degrees"
                    name="angle"
                    model="form.angle"
                />
                <x-input
                    required="true"
                    class="mb-4"
                    label="Grade (mm)"
                    name="grade"
                    model="form.grade"
                />
                <x-input
                    class="mb-4"
                    disabled="true"
                    label="Layer Order"
                    name="layer_order"
                    model="form.layer_order"
                />
            </div>

            <!-- footer -->
            <div class="flex items-center justify-end px-6 py-4 border-t">
                <div class="flex justify-end gap-2">
                    <button  
                        type="button"
                        @click="$dispatch('close')"
                        class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                        Cancel
                    </button>

                    <button 
                        class="px-4 py-2 rounded-lg bg-green-600 text-white 
                            hover:bg-green-700 transition shadow">
                        Save
                    </button>
                </div>
            </div>

        </form>

    <!-- </div> -->
</x-modal>