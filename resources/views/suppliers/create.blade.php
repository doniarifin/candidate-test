<x-app-layout>
    <div class="p-6">

        <h2 class="text-lg font-bold mb-4">Add Supplier</h2>

        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <input type="text" name="name" placeholder="Name"
                class="border p-2 w-full mb-3">

            <input type="text" name="code" placeholder="Code"
                class="border p-2 w-full mb-3">

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </form>

    </div>
</x-app-layout>