<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Deletion Request - {{ env('APP_NAME') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-[Nunito]">
    <div class="sm:p-5 p-2 md:p-20">
        <section class="py-1 bg-blueGray-50">
            <div class="w-full lg:w-8/12 px-4 mx-auto mt-6">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-100 border-0">
                    <div class="rounded-t bg-white mb-0 px-6 py-6">
                        <div class="text-center flex justify-between">
                            <h6 class="text-blueGray-700 text-2xl font-bold">
                                Account Deletion Request
                            </h6>
                        </div>
                    </div>
                    <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
                        @if (session('success'))
                            <div class="mb-5 bg-emerald-400 rounded-lg text-white px-5 py-4 font-semibold">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('submit-account-deletion-request') }}">
                            @csrf
                            <h6 class="text-blueGray-400 text-sm mt-3 mb-6 font-bold uppercase">
                                Account Information
                            </h6>
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-6/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            Email address
                                        </label>
                                        <input type="email" required name="email"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="jesse@example.com">
                                    </div>
                                </div>
                                <div class="w-full lg:w-6/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            Phone Number
                                        </label>
                                        <input type="text" required name="phone_number"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="+91999999999">
                                    </div>
                                </div>
                                <div class="w-full lg:w-6/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            First Name
                                        </label>
                                        <input type="text" required name="first_name"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="Lucky">
                                    </div>
                                </div>
                                <div class="w-full lg:w-6/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            Last Name
                                        </label>
                                        <input type="text" required name="last_name"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="Jesse">
                                    </div>
                                </div>
                                <div class="w-full lg:w-6/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            Reason
                                        </label>
                                        <select required name="reason"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150">
                                            <option selected value="Privacy concerns">Privacy concerns</option>
                                            <option value="Trouble getting started">Trouble getting started</option>
                                            <option value="Concemed about my data">Concemed about my data</option>
                                            <option value="Created a second account">Created a second account</option>
                                            <option value="Too busy/too distracting">Too busy/too distracting</option>
                                            <option value="Want to remove something">Want to remove something</option>
                                            <option value="Something else">Something else</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-6 border-b-1 border-blueGray-300">

                            <div class="flex items-center my-5 px-4">
                                <div class="relative flex items-center">

                                    <input checked type="radio" value="delete" name="type" id="type2"
                                        class="border-0 placeholder-blueGray-300 text-blueGray-600 rounded-full text-sm focus:outline-none ease-linear transition-all duration-150">
                                    <label for="type2" class="uppercase text-blueGray-600 text-xs font-bold ms-1"
                                        htmlfor="grid-password">
                                        Delete
                                    </label>
                                </div>
                                <div class="relative flex items-center ms-5">

                                    <input type="radio" name="type" value="deactivate" id="type"
                                        class="border-0 placeholder-blueGray-300 text-blueGray-600 rounded-full text-sm focus:outline-none ease-linear transition-all duration-150">
                                    <label for="type" class="uppercase text-blueGray-600 text-xs font-bold ms-1"
                                        htmlfor="grid-password">
                                        Deactivate
                                    </label>
                                </div>

                            </div>
                            <div class="flex flex-wrap">
                                <div class="w-full lg:w-12/12 px-4">
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlfor="grid-password">
                                            Request Details
                                        </label>
                                        <textarea type="text" required name="details" placeholder="Enter details here..."
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            rows="4"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full text-center">
                                <button
                                    class="rounded bg-blue-600 w-full max-w-xs py-3 px-5 text-white shadow focus:outline-none focus:ring focus:ring-blue-300 mx-auto ease-linear transition-all duration-150">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
