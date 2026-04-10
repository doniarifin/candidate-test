<x-app-layout>
    <div class="p-6">

        <h2 class="text-lg font-bold mb-4">Edit Supplier</h2>

        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $supplier->name }}"
                class="border p-2 w-full mb-3">

            <input type="text" name="code" value="{{ $supplier->code }}"
                class="border p-2 w-full mb-3">

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update
            </button>
        </form>

    </div>
</x-app-layout>