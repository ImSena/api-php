<?php

namespace App\Controllers;

use App\Http\Response;
use App\Http\Request;
use App\Service\VariationService;

class VariantsController
{
    public function createVariant(Request $request, Response $response)
    {
        $body = $request::body();

        $variationService = VariationService::createVariation($body);

        if (isset($variationService['error'])) {
            return $response::json([
                'success' => false,
                "message" => $variationService['error']
            ]);
        }

        $response::json([
            'success' => true,
            "message" => $variationService
        ]);
    }

    public function getAllVariation(Request $request, Response $response)
    {
        $variationService = VariationService::getAllVariations();

        if (isset($variationService['error'])) {
            return $response::json([
                'success' => false,
                "message" => $variationService['error']
            ]);
        }

        $response::json([
            'success' => true,
            "message" => $variationService['message'],
            "content" => $variationService['content']
        ]);
    }

    public function updateVariation(Request $request, Response $response, $id)
    {
        $body = $request::body();
        $id = intval($id[0]);

        $variationService = VariationService::updateVariation($body, $id);

        if (isset($variationService['error'])) {
            return $response::json([
                'success' => false,
                "message" => $variationService['error']
            ]);
        }

        $response::json([
            'success' => true,
            "message" => $variationService
        ]);
    }

    public function deleteVariation(Request $request, Response $response, $id)
    {
        $id = intval($id[0]);

        $variationService = VariationService::deleteVariation($id);

        if (isset($variationService['error'])) {
            return $response::json([
                'success' => false,
                "message" => $variationService['error']
            ]);
        }

        $response::json([
            'success' => true,
            "message" => $variationService
        ]);
    }

    public function addValueVariation(Request $request, Response $response) 
    {
        $body = $request::body();

        $variationService = VariationService::createValue($body);

        if (isset($variationService['error'])) {
            return $response::json([
                'success' => false,
                "message" => $variationService['error']
            ]);
        }

        $response::json([
            'success' => true,
            "message" => $variationService
        ]);
    }

    public function getValueVariation(Request $request, Response $response, $id) {}

    public function updateValueVariation(Request $request, Response $response, $id) {}

    public function deleteValueVariation(Request $request, Response $response, $id) {}
}
