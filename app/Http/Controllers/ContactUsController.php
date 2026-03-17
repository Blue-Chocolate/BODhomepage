<?php

namespace App\Http\Controllers;

use App\Actions\ContactUs\DeleteContactUsAction;
use App\Actions\ContactUs\StoreContactUsAction;
use App\Actions\ContactUs\UpdateContactUsAction;
use App\Http\Requests\ContactUs\StoreContactUsRequest;
use App\Models\ContactUs;
use App\Repositories\ContactUsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(
        private readonly ContactUsRepository $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 15), 100);
        $contacts = $this->repository->getAll($perPage);

        return response()->json($contacts);
    }

    public function show(int $id): JsonResponse
    {
        $contact = $this->repository->findById($id);

        return response()->json($contact);
    }

    public function store(StoreContactUsRequest $request, StoreContactUsAction $action): JsonResponse
    {
        $contact = $action->execute($request->validated());

        return response()->json($contact, 201);
    }

    public function update(Request $request, ContactUs $contactUs, UpdateContactUsAction $action): JsonResponse
    {
        $request->validate([
            'name'    => ['sometimes', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['sometimes', 'email', 'max:255'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string'],
            'reply'   => ['nullable', 'string'],
        ]);

        $contact = $action->execute($contactUs, $request->validated());

        return response()->json($contact);
    }

    public function destroy(ContactUs $contactUs, DeleteContactUsAction $action): JsonResponse
    {
        $action->execute($contactUs);

        return response()->json(['message' => 'Deleted successfully']);
    }
}