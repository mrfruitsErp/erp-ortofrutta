<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Product;
use App\Models\ClientProductOverride;
use App\Models\PriceListItem;

class OrderRulesService
{
    public function resolve(Client $client, Product $product): array
    {
        $result = [
            'prezzo'           => (float) $product->price,
            'modalita'         => $product->modalita_vendita,
            'min_qty'          => (float) ($product->ordine_min ?? 1),
            'max_qty'          => $product->ordine_max ? (float) $product->ordine_max : null,
            'min_qty_kg'       => $product->ordine_min_kg ? (float) $product->ordine_min_kg : null,
            'puo_ordinare_kg'  => false,
            'bloccato'         => false,
            'prezzo_originale' => (float) $product->price,
            'sconto_applicato' => 0,
            'fonte_prezzo'     => 'prodotto',
        ];

        // LIVELLO 2: Listino
        $priceList = $client->priceList;
        if ($priceList) {
            $result['puo_ordinare_kg'] = (bool) $priceList->puo_ordinare_kg;

            if ($priceList->sconto_default_pct > 0) {
                $result['prezzo'] = round($product->price * (1 - $priceList->sconto_default_pct / 100), 2);
                $result['sconto_applicato'] = (float) $priceList->sconto_default_pct;
                $result['fonte_prezzo'] = 'listino_sconto_globale';
            }

            $listItem = PriceListItem::where('price_list_id', $priceList->id)
                ->where('product_id', $product->id)->first();

            if ($listItem) {
                if ($listItem->bloccato) {
                    $result['bloccato'] = true;
                    return $result;
                }
                if ($listItem->prezzo_override !== null) {
                    $result['prezzo'] = (float) $listItem->prezzo_override;
                    $result['sconto_applicato'] = $product->price > 0
                        ? round((1 - $listItem->prezzo_override / $product->price) * 100, 2) : 0;
                    $result['fonte_prezzo'] = 'listino_prezzo_fisso';
                } elseif ($listItem->sconto_pct !== null) {
                    $result['prezzo'] = round($product->price * (1 - $listItem->sconto_pct / 100), 2);
                    $result['sconto_applicato'] = (float) $listItem->sconto_pct;
                    $result['fonte_prezzo'] = 'listino_sconto_prodotto';
                }
                if ($listItem->min_qty !== null)    $result['min_qty'] = (float) $listItem->min_qty;
                if ($listItem->max_qty !== null)    $result['max_qty'] = (float) $listItem->max_qty;
                if ($listItem->min_qty_kg !== null)  $result['min_qty_kg'] = (float) $listItem->min_qty_kg;
            }
        }

        // LIVELLO 3: Override cliente
        if ($client->puo_ordinare_kg !== null) {
            $result['puo_ordinare_kg'] = (bool) $client->puo_ordinare_kg;
        }

        // LIVELLO 4: Override cliente-prodotto
        $override = ClientProductOverride::where('client_id', $client->id)
            ->where('product_id', $product->id)->first();

        if ($override) {
            if ($override->bloccato) {
                $result['bloccato'] = true;
                return $result;
            }
            if ($override->prezzo_override !== null) {
                $result['prezzo'] = (float) $override->prezzo_override;
                $result['sconto_applicato'] = $product->price > 0
                    ? round((1 - $override->prezzo_override / $product->price) * 100, 2) : 0;
                $result['fonte_prezzo'] = 'override_cliente';
            }
            if ($override->min_override !== null)      $result['min_qty'] = (float) $override->min_override;
            if ($override->max_override !== null)       $result['max_qty'] = (float) $override->max_override;
            if ($override->modalita_override !== null)  $result['modalita'] = $override->modalita_override;
        }

        return $result;
    }

    public function resolveAll(Client $client): array
    {
        $products = Product::ordinabili()->orderBy('category')->orderBy('name')->get();
        $rules = [];
        foreach ($products as $product) {
            $rule = $this->resolve($client, $product);
            if (!$rule['bloccato']) {
                $rules[$product->id] = array_merge($rule, ['product' => $product]);
            }
        }
        return $rules;
    }
}
