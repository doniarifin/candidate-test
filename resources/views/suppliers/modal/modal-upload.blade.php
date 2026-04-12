<x-modal name="modal-upload" :closeable="false">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- <h1 x-text="supplierId"></h1> -->

        <!-- head -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Import Layup Data
            </h2>

            <button type="button" @click="cancelImport" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- body -->
        <div class="p-6 space-y-5 overflow-y-auto">

            <!-- upload -->
            <input 
                type="file" 
                x-ref="fileInput" 
                class="hidden"
                :key="fileKey"
                accept=".json"
                @change="handleFile"
            />
            <div @click="$refs.fileInput.click()" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition cursor-pointer">
                
                <div class="flex flex-col items-center space-y-2">
                    <div class="text-green-600 text-2xl"></div>

                    <p class="text-sm text-gray-600">
                        <span class="text-green-600 font-medium">Click to upload</span>
                    </p>

                    <p class="text-xs text-gray-400">
                        JSON up to 10MB
                    </p>
                </div>

            </div>

            <!-- preview -->
            <div x-show="fileUrl" class="text-center">
                <a 
                    :href="fileUrl" 
                    download 
                    class="text-sm text-green-600 hover:underline"
                >
                    Preview / Download file
                </a>
            </div>

            <!-- action -->
            <div>
                <x-input-select 
                    class="mt-2 text-sm"
                    label="Conflict Resolution Strategy"
                    name="conflict"
                    model="action"
                    :options="[
                        'skip' => 'Skip Conflicts (Default)',
                        'overwrite' => 'Overwrite Existing',
                        'duplicate' => 'Duplicate Layup',
                        'reject' => 'Reject Entire Import'
                    ]"
                />
            </div>

            <!-- dry run -->
            <div class="flex items-start gap-3 border rounded-lg p-4">
                <input x-model="dryRun" type="checkbox" class="mt-1">

                <div class="text-sm">
                    <p class="text-gray-700 font-medium">Run as Dry Run</p>
                    <p class="text-gray-400 text-xs">
                        Simulate the import process without saving changes to the database.
                    </p>
                </div>

                <div class="ml-auto text-gray-400"></div>
            </div>

            <!-- warn -->
            <div x-show="conflicts.length > 0" class="border border-red-200 bg-red-50 rounded-lg p-4 flex gap-3">
                
                <div class="text-red-500">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="text-sm">
                    <p class="text-red-600 font-semibold">
                        Potential Conflicts Detected
                    </p>
                    <div class="flex text-red-500 text-xs">
                        <span x-text="conflicts.length"></span>&nbsp Layups differ significantly from current suppliers in the database.&nbsp
                        <span class="text-xs" @click="openModalConflict">
                            <a href="#" class="underline">View details</a>
                        </span>
                    </div>
                </div>

            </div>

            <div x-show="conflicts.length === 0 && isCheckedConflict" class="border border-green-200 bg-green-50 rounded-lg p-4 flex gap-3">
                
                <div class="text-green-500">
                    <i class="fa-regular fa-circle-check"></i>
                </div>

                <div  class="text-sm">
                    <p class="text-green-600 font-semibold">
                        No Conflicts Detected
                    </p>
                    <p class="text-green-500 text-xs">
                        Everything looks good, no conflicts found in database.
                    </p>
                </div>

            </div>
        </div>

        <!-- foot -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50">
            <button
                type="button" 
                @click="cancelImport"
                class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
                Cancel
            </button>

            <button
                :disabled="!fileUrl"
                @click="submitImport"
                :class="{
                    'bg-gray-400 cursor-not-allowed hover:bg-gray-400': !fileUrl,
                    'bg-green-600 hover:bg-green-700': fileUrl
                }"
                class="px-4 py-2 rounded-lg text-white flex items-center gap-2">
                Confirm Import
            </button>

        </div>
        
    </div>
</x-modal>