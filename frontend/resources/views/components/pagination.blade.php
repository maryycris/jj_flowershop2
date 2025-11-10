@props(['currentPage' => 1, 'totalPages' => 1, 'baseUrl' => '', 'queryParams' => []])

@php
    $maxVisiblePages = 5;
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
    
    if ($endPage - $startPage + 1 < $maxVisiblePages) {
        $startPage = max(1, $endPage - $maxVisiblePages + 1);
    }
@endphp

@if($totalPages > 1)
<div class="pagination-container d-flex justify-content-center mt-4 mb-4">
    <nav aria-label="Product pagination">
        <ul class="pagination pagination-custom mb-0">
            <!-- Previous Button -->
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $currentPage > 1 ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage - 1])) : '#' }}" 
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <!-- First Page (if not visible) -->
            @if($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => 1])) }}">1</a>
                </li>
                @if($startPage > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif
            
            <!-- Page Numbers -->
            @for($i = $startPage; $i <= $endPage; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $i])) }}">{{ $i }}</a>
                </li>
            @endfor
            
            <!-- Last Page (if not visible) -->
            @if($endPage < $totalPages)
                @if($endPage < $totalPages - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $totalPages])) }}">{{ $totalPages }}</a>
                </li>
            @endif
            
            <!-- Next Button -->
            <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $currentPage < $totalPages ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $currentPage + 1])) : '#' }}" 
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.pagination-custom .page-link {
    color: #7bb47b;
    background-color: #fff;
    border: 1px solid #e6f4ea;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-custom .page-link:hover {
    color: #fff;
    background-color: #7bb47b;
    border-color: #7bb47b;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(123, 180, 123, 0.3);
}

.pagination-custom .page-item.active .page-link {
    color: #fff;
    background-color: #7bb47b;
    border-color: #7bb47b;
    box-shadow: 0 2px 8px rgba(123, 180, 123, 0.4);
}

.pagination-custom .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-custom .page-item.disabled .page-link:hover {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    transform: none;
    box-shadow: none;
}

.pagination-container {
    background: rgba(255, 255, 255, 0.9);
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin: 1rem 0;
}
</style>
@endif
