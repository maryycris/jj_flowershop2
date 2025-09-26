@extends('layouts.clerk_app')
@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="bg-white rounded-3 p-3" style="box-shadow:none;">
                <ul class="nav nav-tabs" id="customizeTabs" role="tablist">
                    @foreach($categories as $i => $cat)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($i==0) active @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ strtolower($cat) }}" type="button" role="tab">{{ $cat }}</button>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content border-start border-end border-bottom rounded-bottom p-3" id="customizeTabContent">
                    @foreach($categories as $i => $cat)
                    <div class="tab-pane fade @if($i==0) show active @endif" id="tab-{{ strtolower($cat) }}" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $cat }} Items</h5>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal" data-category="{{ $cat }}"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                        <div class="row g-3">
                            @foreach(($items[$cat] ?? []) as $item)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    @if($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" class="card-img-top" style="height:140px;object-fit:cover;">
                                    @endif
                                    <div class="card-body p-2">
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <div class="text-muted small">₱{{ number_format($item->price ?? 0,2) }}</div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between p-2">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Edit</button>
                                        <form method="POST" action="{{ route('clerk.customize.destroy',$item->id) }}" onsubmit="return confirm('Delete this item?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                              <div class="modal-dialog">
                                <form class="modal-content" method="POST" action="{{ route('clerk.customize.update',$item->id) }}" enctype="multipart/form-data">
                                  @csrf @method('PUT')
                                  <div class="modal-header"><h5 class="modal-title">Edit {{ $cat }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                  <div class="modal-body">
                                    <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $item->name }}" required></div>
                                    <div class="mb-2"><label class="form-label">Category</label>
                                        <select name="category" class="form-select">
                                            @foreach($categories as $c)<option value="{{ $c }}" @if($item->category==$c) selected @endif>{{ $c }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2"><label class="form-label">Price (optional)</label><input type="number" step="0.01" name="price" class="form-control" value="{{ $item->price }}"></div>
                                    <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control"></div>
                                    <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea></div>
                                  </div>
                                  <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
                                </form>
                              </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal (shared) -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('clerk.customize.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Add Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Category</label>
            <select name="category" id="addCategorySelect" class="form-select">
                @foreach($categories as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
            </select>
        </div>
        <div class="mb-2"><label class="form-label">Price (optional)</label><input type="number" step="0.01" name="price" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Add</button></div>
    </form>
  </div>
  </div>

@push('scripts')
<script>
document.getElementById('addModal')?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (btn && btn.dataset.category) {
        document.getElementById('addCategorySelect').value = btn.dataset.category;
    }
});
</script>
@endpush
@endsection


