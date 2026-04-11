<x-modal x-data="layupManager()" name="modal-upload" :closeable="false">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- head -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Import Layup Data
            </h2>

            <button type="button" @click="$helper.closeModal('modal-upload')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- body -->
        <div class="p-6 space-y-5">

            <!-- upload -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition cursor-pointer">
                
                <div class="flex flex-col items-center space-y-2">
                    <div class="text-green-600 text-2xl"></div>

                    <p class="text-sm text-gray-600">
                        <span class="text-green-600 font-medium">Click to upload</span>
                        or drag and drop
                    </p>

                    <p class="text-xs text-gray-400">
                        CSV or JSON up to 10MB
                    </p>
                </div>

            </div>

            <!-- CONFLICT STRATEGY -->
            <div>
                <label class="text-sm text-gray-600">Conflict Resolution Strategy</label>

                <select class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-green-200">
                    <option>Skip conflicts (Default)</option>
                    <option>Overwrite existing</option>
                    <option>Keep both</option>
                </select>
            </div>

            <!-- DRY RUN -->
            <div class="flex items-start gap-3 border rounded-lg p-4">
                <input type="checkbox" class="mt-1">

                <div class="text-sm">
                    <p class="text-gray-700 font-medium">Run as Dry Run</p>
                    <p class="text-gray-400 text-xs">
                        Simulate the import process without saving changes to the database.
                    </p>
                </div>

                <div class="ml-auto text-gray-400"></div>
            </div>

            <!-- warn -->
            <div class="border border-red-200 bg-red-50 rounded-lg p-4 flex gap-3">
                
                <div class="text-red-500">Alert</div>

                <div class="text-sm">
                    <p class="text-red-600 font-semibold">
                        Potential Conflicts Detected
                    </p>
                    <p class="text-red-500 text-xs">
                        3 Layups differ significantly from current suppliers in the database.
                        <a href="#" class="underline">View details</a>
                    </p>
                </div>

            </div>

        </div>

        <!-- foot -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50">
            <button
                type="button" 
                @click="$helper.closeModal('modal-upload')"
                class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
                Cancel
            </button>

            <button
                @click="$helper.openModal('modal-conflict')"
                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 flex items-center gap-2">
                Confirm Import
            </button>

        </div>
        
    </div>
</x-modal>