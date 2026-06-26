{{--
  Usage:
  @include('partials.form-card', [
      'title'  => 'Add expense',
      'action' => route('expenses.store'),
      'method' => 'POST',       // optional, default POST
      'model'  => $expense,     // optional, for edit forms
      'back'   => route('expenses.index'),
  ])
  Then in the slot: use @section / @endsection pattern (see individual form views)
--}}