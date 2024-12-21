@extends('admin.layout.app')

@section('content')
    <style>
        .truncate {
            max-width: 155px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="card">

                    @if (session('message'))
                        <div id="message" class="alert alert-success">{{ session('message') }}</div>
                    @elseif(session('error'))
                        <div id="message" class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="d-flex align-items-end row">
                        <div class="col">
                            <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Users Info</h3>
                                <a href="{{ route('add_user') }}"><button type="button" class="btn btn-primary">Add
                                        User</button></a>
                            </div>
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <form action="{{ route('manage_user') }}" method="get" class="d-flex">
                                            <!-- Updated to single 'name' field for searching full name -->
                                            <input type="text" name="name" value="{{ old('name', $nameQuery) }}"
                                                placeholder="Name" class="form-control me-2">
                                            <input type="text" name="email" value="{{ old('email', $emailQuery) }}"
                                                placeholder="Email" class="form-control me-2">
                                            <button type="submit" class="btn btn-dark">Search</button>
                                            <button type="button" class="btn btn-outline-dark ms-2"><a
                                                    href="{{ route('manage_user') }}"
                                                    class="text-decoration-none text-reset">Reset</a></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive mar">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User Name</th>
                                                <th>Email Address</th>
                                                <th>Mobile No.</th>
                                                <th>Country</th>
                                                <th>Created On</th>
                                                <th>Download</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td class="truncate">{{ $user->email }}</td>
                                                    <td>{{ optional($user->user_profile)->phone ?? 'N/A' }}</td>
                                                    <td>{{ optional($user->user_profile)->country ?? 'N/A' }}</td>
                                                    <!-- Updated line -->
                                                    <td>{{ $user->created_at }}</td>
                                                    <td>
                                                        @if (!empty($user->upload->f_path))
                                                            <a href="{{ route('download', ['id' => $user->id]) }}"
                                                                class="btn btn-success btn-sm">Download</a>
                                                        @else
                                                            <span>No file exists</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('edit_user', ['encrypted_id' => $user->encrypted_id]) }}"
                                                            class="btn btn-warning btn-sm">Edit</a>
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#confirmDeleteModal">Delete</button>
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#uploadFileModal"
                                                            onclick="setUserId({{ $user->id }})">Upload</button>
                                                    </td>
                                                </tr>

                                                <!-- Modal for Deletion Confirmation -->
                                                <div class="modal fade" id="confirmDeleteModal" tabindex="-1"
                                                    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="confirmDeleteModalLabel">
                                                                    Warning?</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this user?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <!-- Updated button here with correct onclick handler -->
                                                                <a type="button" class="ms-3 btn btn-danger"
                                                                    href="{{ route('delete_user', ['id' => $user->id]) }}">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal for file upload -->
                                                <div class="modal fade" id="uploadFileModal" tabindex="-1"
                                                    aria-labelledby="uploadFileModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="uploadFileModalLabel">Upload
                                                                    File Form</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('upload') }}" method="POST"
                                                                    enctype="multipart/form-data">
                                                                    @csrf
                                                                    <!-- Hidden field to pass user_id -->
                                                                    <input type="hidden" id="user_id" name="user_id"
                                                                        value="{{ $user->id }}">

                                                                    <div class="mb-3">
                                                                        <label for="filename" class="form-label">File Name
                                                                            <span class="error">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            id="filename" name="filename"
                                                                            placeholder="Enter file name"
                                                                            value="{{ old('filename') }}" autofocus />
                                                                    </div>

                                                                    <div class="mb-5">
                                                                        <label for="file" class="form-label">Upload
                                                                            File
                                                                            <span class="error">*</span></label>
                                                                        <input type="file" class="form-control"
                                                                            id="file" name="file" autofocus />
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <button class="btn btn-primary d-grid float-end"
                                                                            type="submit">Upload</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            @empty
                                                <tr>
                                                    <td colspan="8">No users found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination Links -->
                                <nav class="d-flex justify-content-end mt-5 mb-5">
                                    <ul class="pagination">
                                        @if ($users->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link"
                                                    style="border-radius: 5px;">Previous</span></li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->previousPageUrl() }}"
                                                    style="border-radius: 5px;">Previous</a>
                                            </li>
                                        @endif

                                        <!-- Pagination Numbers -->
                                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                            <li class="page-item {{ $users->currentPage() == $page ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}"
                                                    style="border-radius: 5px;">{{ $page }}</a>
                                            </li>
                                        @endforeach

                                        @if ($users->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->nextPageUrl() }}"
                                                    style="border-radius: 5px;">Next</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled"><span class="page-link"
                                                    style="border-radius: 5px;">Next</span></li>
                                        @endif
                                    </ul>
                                </nav>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                window.location.href = "/admin/delete_user/" + userId;
            }
        }
    </script>

    <script>
        function setUserId(userId) {
            document.getElementById('user_id').value = userId;
        }
    </script>
@endsection
