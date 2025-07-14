<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\TranslationService;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Translation Management Service API",
 *     version="1.0.0",
 *     description="API for managing translations with multi-language support"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for authentication"
 * )
 * @OA\Tag(
 *     name="Translations",
 *     description="API Endpoints for managing translations"
 * )
 */
class TranslationController extends Controller
{
    public function __construct(
        private TranslationService $service
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Get a translation by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Translation")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     */
    public function show(int $id)
    {
        $translation = $this->service->getTranslation($id);

        return $translation
            ? response()->json($translation)
            : response()->json(['error' => 'Not found'], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     tags={"Translations"},
     *     summary="Create a new translation",
     *     description="Stores a new translation record in the database",
     *     operationId="createTranslation",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation data to store",
     *         @OA\JsonContent(
     *             required={"group", "key", "value", "locale"},
     *             @OA\Property(property="group", type="string", example="validation", description="The translation group/category"),
     *             @OA\Property(property="key", type="string", example="required", description="The translation key"),
     *             @OA\Property(property="value", type="string", example="This field is required", description="The translated text"),
     *             @OA\Property(property="locale", type="string", example="en", description="Language code (2 characters)"),
     *             @OA\Property(property="tag", type="string", nullable=true, example="web", description="Optional context tag")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Translation",
     *             example={
     *                 "id": 1,
     *                 "group": "validation",
     *                 "key": "required",
     *                 "value": "This field is required",
     *                 "locale": "en",
     *                 "tag": "web",
     *                 "created_at": "2023-07-15T10:00:00.000000Z",
     *                 "updated_at": "2023-07-15T10:00:00.000000Z"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "group": {"The group field is required"},
     *                     "locale": {"The locale must be 2 characters"}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Duplicate translation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Translation already exists for this group/key/locale combination")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'locale' => 'required|string|size:2',
            'tag' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $translation = $this->service->createTranslation($validator->validated());

        return response()->json($translation, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Update a translation",
     *     description="Updates an existing translation record",
     *     operationId="updateTranslation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the translation to update",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation data to update",
     *         @OA\JsonContent(
     *             required={"group", "key", "value", "locale"},
     *             @OA\Property(property="group", type="string", example="validation"),
     *             @OA\Property(property="key", type="string", example="required"),
     *             @OA\Property(property="value", type="string", example="This field is required"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="tag", type="string", nullable=true, example="web")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Translation")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="field_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The field name is required")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Translation not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'value' => 'sometimes|string',
            'locale' => 'sometimes|string|size:2',
            'tag' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $success = $this->service->updateTranslation($id, $validator->validated());

        return $success
            ? response()->json(['message' => 'Translation updated'])
            : response()->json(['error' => 'Not found'], 404);
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Deletes an existing translation by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Translation ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Translation")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Translation not found"
     *      )
     * )
     */
    public function destroy(int $id)
    {
        $success = $this->service->deleteTranslation($id);

        return $success
            ? response()->json(['message' => 'Translation deleted'])
            : response()->json(['error' => 'Not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/search",
     *     tags={"Translations"},
     *     summary="Search translations",
     *     description="Search translations by query, tag, or locale",
     *     operationId="searchTranslations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search term to look for in key or value",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filter by tag (web, mobile, desktop, tablet)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Filter by locale (en, fr, es)",
     *         required=false,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Translation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $results = $this->service->searchTranslations(
            $request->get('query'),
            $request->get('tag'),
            $request->get('locale')
        );

        return response()->json($results);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     tags={"Translations"},
     *     summary="Export translations",
     *     description="Export translations in JSON format grouped by locale and group",
     *     operationId="exportTranslations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filter by tag (web, mobile, desktop, tablet)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Filter by specific locale",
     *         required=false,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "en": {
     *                     "validation": {
     *                         "required": "This field is required"
     *                     }
     *                 },
     *                 "fr": {
     *                     "validation": {
     *                         "required": "Ce champ est obligatoire"
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function export(Request $request)
    {
        $translations = $this->service->exportTranslations(
            $request->get('tag'),
            $request->get('locale')
        );

        return response()->json($translations);
    }
}
