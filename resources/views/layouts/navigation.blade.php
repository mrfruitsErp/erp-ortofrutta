<nav class="bg-white border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            <div class="flex">

                <div class="shrink-0 flex items-center">
                    <a href="/documents">
                        ERP
                    </a>
                </div>


                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                    <a href="/documents" class="text-gray-700">
                        Documenti
                    </a>

                    <a href="/clients" class="text-gray-700">
                        Clienti
                    </a>

                    <a href="/products" class="text-gray-700">
                        Prodotti
                    </a>

                </div>

            </div>


            <div class="hidden sm:flex sm:items-center sm:ml-6">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit">
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>