<?php

/*
 * Copyright BibLibre, 2016
 * Copyright Daniel Berthereau, 2019-2020
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace Basket\Controller\Site;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class BasketController extends AbstractActionController
{
    public function addAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->jsonErrorNotFound();
        }

        $params = $this->params();
        $id = $params->fromRoute('id') ?: $params->fromQuery('id');
        if (!$id) {
            return $this->jsonErrorNotFound();
        }

        $isMultiple = is_array($id);
        $ids = $isMultiple ? $id : [$id];

        $api = $this->api();

        // Check resources.
        $resources = [];
        foreach ($ids as $id) {
            try {
                $resource = $api->read('resources', ['id' => $id])->getContent();
            } catch (\Omeka\Api\Exception\NotFoundException $e) {
                return $this->jsonErrorNotFound();
            }
            $resources[$id] = $resource;
        }

        $user = $this->identity();
        $userId = $user->getId();
        $updateBasketLink = $this->viewHelpers()->get('updateBasketLink');
        $results = [];

        foreach ($resources as $resourceId => $resource) {
            $basketItem = $api->searchOne('basket_items', ['user_id' => $userId, 'resource_id' => $resourceId])->getContent();
            $data = [
                'content' => $updateBasketLink($resource, ['basketItem' => $basketItem, 'action' => 'delete']),
            ];
            if ($basketItem) {
                $data['status'] = 'fail';
                $data['message'] = $this->translate('Already in'); // @translate
            } else {
                $data['status'] = 'success';
                $basketItem = $api->create('basket_items', ['o:user_id' => $userId, 'o:resource_id' => $resourceId])->getContent();
            }
            $results[$resourceId] = $data;
        }

        if ($isMultiple) {
            $data = [
                'basket_items' => $results,
            ];
        } else {
            $data = [
                'basket_item' => reset($results),
            ];
        }

        return new JsonModel([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function deleteAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->jsonErrorNotFound();
        }

        $params = $this->params();
        $id = $params->fromRoute('id') ?: $params->fromQuery('id');
        if (!$id) {
            return $this->jsonErrorNotFound();
        }

        $isMultiple = is_array($id);
        $ids = $isMultiple ? $id : [$id];

        $api = $this->api();

        $user = $this->identity();
        $userId = $user->getId();
        $updateBasketLink = $this->viewHelpers()->get('updateBasketLink');
        $results = [];

        foreach ($ids as $resourceId) {
            $data = [
                'user_id' => $userId,
                'resource_id' => $resourceId,
            ];
            $basketItem = $api->searchOne('basket_items', $data)->getContent();
            if ($basketItem) {
                $resource = $basketItem->resource();
                $api->delete('basket_items', $basketItem->id());
                $results[$resourceId] = [
                    'status' => 'success',
                    'content' => $updateBasketLink($resource, ['basketItem' => null, 'action' => 'add']),
                ];
            } else {
                $results[$resourceId] = [
                    'status' => 'error',
                    'message' => $this->translate('Not found'), // @translate
                ];
            }
        }

        if ($isMultiple) {
            $data = [
                'basket_items' => $results,
            ];
        } else {
            $data = [
                'basket_item' => reset($results),
            ];
        }

        return new JsonModel([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function toggleAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->jsonErrorNotFound();
        }

        $params = $this->params();
        $id = $params->fromRoute('id') ?: $params->fromQuery('id');
        if (!$id) {
            return $this->jsonErrorNotFound();
        }

        $isMultiple = is_array($id);
        $ids = $isMultiple ? $id : [$id];

        $api = $this->api();

        // Check resources.
        $resources = [];
        foreach ($ids as $id) {
            try {
                $resource = $api->read('resources', ['id' => $id])->getContent();
                $resources[$id] = $resource;
            } catch (\Omeka\Api\Exception\NotFoundException $e) {
            }
        }

        if (!count($resources)) {
            return $this->jsonErrorNotFound();
        }

        $user = $this->identity();
        $userId = $user->getId();
        $updateBasketLink = $this->viewHelpers()->get('updateBasketLink');

        $results = [];
        $add = [];
        /** @var \Basket\Api\Representation\BasketItemRepresentation[] $delete */
        $delete = [];
        foreach ($resources as $resourceId => $resource) {
            $data = ['user_id' => $userId, 'resource_id' => $resourceId];
            $response = $api->searchOne('basket_items', $data);
            if ($response->getTotalResults()) {
                $delete[$resourceId] = $response->getContent();
            } else {
                $add[$resourceId] = $resourceId;
            }
        }

        if ($add) {
            foreach ($add as $resourceId) {
                $basketItem = $api->create('basket_items', ['o:user_id' => $userId, 'o:resource_id' => $resourceId])->getContent();
                $results[$resourceId] = [
                    'status' => 'success',
                    'content' => $updateBasketLink($resource, ['basketItem' => $basketItem, 'action' => 'toggle']),
                ];
            }
        }

        if ($delete) {
            foreach ($delete as $resourceId => $basketItem) {
                $api->delete('basket_items', $basketItem->id());
                $results[$resourceId] = [
                    'status' => 'success',
                    'content' => $updateBasketLink($resources[$resourceId], ['basketItem' => null, 'action' => 'toggle']),
                ];
            }
        }

        if ($isMultiple) {
            $data = [
                'basket_items' => $results,
            ];
        } else {
            $data = [
                'basket_item' => reset($results),
            ];
        }

        return new JsonModel([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    protected function jsonErrorNotFound()
    {
        $response = $this->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_404);
        return new JsonModel([
            'status' => 'error',
            'message' => $this->translate('Not found'), // @translate
        ]);
    }
}
