<x-modal name="delete-layer" maxWidth="md" :closeable="false">
    <div class="justify-end">
      
    </div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">

        
        <div class="flex justify-between">
          <h2 class="text-lg font-semibold text-red-600">
              Delete Layer
          </h2>
          <button 
            type="button" 
            @click="$dispatch('close')"
            class="justify-end text-gray-400 hover:text-gray-600 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        </div>

        <p class="text-sm text-gray-600 mt-3">
            Are you sure you want to delete this layer?
            This action cannot be undone.
        </p>

        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
            <p><strong>Thickness:</strong> <span x-text="selectedLayer?.thickness"></span> mm</p>
            <p><strong>Angle:</strong> <span x-text="selectedLayer?.angle"></span>°</p>
        </div>

        <div class="flex justify-end gap-2 mt-6">

            <button @click="closeDeleteModal()"
                class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                Cancel
            </button>

            <button @click="deleteLayer()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                Delete
            </button>

        </div>

    </div>
</x-modal>