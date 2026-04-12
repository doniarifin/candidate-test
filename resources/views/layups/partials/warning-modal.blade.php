<x-modal name="warning-modal" maxWidth="md" :closeable="false">
    <div class="justify-end">
      
    </div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">

        <div class="flex justify-between">
          <h2 class="text-lg font-semibold text-red-600">
              Warning
          </h2>
          <button 
            type="button"
            @click="closeModalWarning()"
            class="justify-end text-gray-400 hover:text-gray-600 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        </div>

        <p class="text-sm text-gray-600 mt-3" x-text="warningMessage">
            
        </p>

        <div class="flex justify-end gap-2 mt-6">

             <button 
                type="button"
                @click="closeModalWarning()"
                class="px-4 py-2 border rounded-lg">
                No
            </button>

            <button 
                type="button"
                @click="duplicateLayup()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg">
                Yes
            </button>

        </div>

    </div>
</x-modal>