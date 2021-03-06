<?php

namespace Jcart\Framework\Cart;

use Jcart\Framework\Cart\Product as CartFacadeProduct;
// use Jcart\Framework\Models\Database\Attribute;
// use Jcart\Framework\Models\Database\ProductAttributeIntegerValue;
use App\Models\Product;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

class Manager
{
    /**
     * Jcart Cart Session Manager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    public $session;

    /**
     * Jcart Cart Construct.
     *
     * @var \Illuminate\Session\SessionManager
     */
    public function __construct(SessionManager $manager)
    {
        $this->session = $manager;
    }

    /**
     * Add Product into cart using Slug and Qty.
     *
     * @param string  $slug
     * @param int $qty
     * @param array $attributes
     * @return \Jcart\Framework\Cart\Manager $this
     */
    public function add($slug, $qty, $attribute = null): Manager
    {
        $cartProducts = $this->getSession();
        $product    = Product::whereSlug($slug)->first();
        $price      = $product->price;
        $attributes = null;

        if (null !== $attribute && count($attribute)) {
            foreach ($attribute as $attributeId => $variationId) {
                $variableProduct    = Product::find($variationId);
                $attributeModel     = Attribute::find($attributeId);

                $productAttributeIntValModel = ProductAttributeIntegerValue::
                                                        whereAttributeId($attributeId)
                                                        ->whereProductId($variableProduct->id)
                                                        ->first();
                $optionModel = $attributeModel
                                    ->AttributeDropdownOptions()
                                    ->whereId($productAttributeIntValModel->value)
                                    ->first();


                $price              = $variableProduct->price;
                $attributes[] = [
                                'attribute_id' => $attributeId,
                                'variation_id' => $variationId,
                                'attribute_dropdown_option_id' => $optionModel->id,
                                'variation_display_text' => $attributeModel->name. ": " . $optionModel->display_text
                            ];
            }
        }

        $cartProduct = new CartFacadeProduct();
        $cartProduct->name($product->name)
                    ->qty($qty)
                    ->slug($slug)
                    ->price($price)
                    ->image($product->image)
                    ->lineTotal($qty * $price)
                    ->attributes($attributes);

        $cartProducts->put($slug, $cartProduct);

        $this->session->put($this->getSessionKey(), $cartProducts);

        return $this;
    }

    /**
     * Update the Cart Product Qty by Slug.
     *
     * @param string  $slug
     * @param int $qty
     * @param array $attribute
     * @return boolean
     */
    public function canAddToCart($slug, $qty, $attribute = [])
    {
        $cartProducts = $this->getSession();
        $cartProduct = $cartProducts->get($slug);
        $cartQty = $cartProduct ? $cartProduct->qty() : 0;

        $checkQty = $qty +  $cartQty;
        $product = Product::whereSlug($slug)->first();

        $productQty = $product->qty;
        if ($productQty >= $checkQty) {
            return true;
        }

        return false;
    }
    /**
     * Update the Cart Product Qty by Slug.
     *
     * @param string  $slug
     * @param int $qty
     * @return \Jcart\Framework\Cart\Manager $this
     */
    public function update($slug, $qty): Manager
    {
        $cartProducts = $this->getSession();

        $cartProduct = $cartProducts->get($slug);

        if (null === $cartProduct) {
            throw new \Exception("Cart Product doesn't Exist");
        }
        $cartProduct->qty($qty);
        $cartProduct->lineTotal($qty * $cartProduct->price());

        return $this;
    }

    /**
     * Update the Cart Product Qty by Slug.
     *
     * @param string    $slug
     * @param float     $amount
     * @return \Jcart\Framework\Cart\Manager $this
     */
    public function updateProductTax($slug, $amount): Manager
    {
        $cartProducts = $this->getSession();

        $cartProduct = $cartProducts->get($slug);

        if (null === $cartProduct) {
            throw new \Exception("Cart Product doesn't Exist");
        }
        $cartProduct->tax($amount);

        $cartProduct->lineTotal($cartProduct->qty() * $cartProduct->price() + $amount);

        return $this;
    }

    /**
     * Clear the All Cart Products.
     *
     * @return void
     */
    public function clear()
    {
        $session = $this->getSessionKey();
        $this->session->forget($session);
    }

    /**
     * Remove an Item from Cart Products by Slug.
     *
     * @param string $slug
     * @return void
     */
    public function destroy($slug):Manager
    {
        $cartProducts = $this->getSession();

        $cartProduct = $cartProducts->pull($slug);

        return $this;
    }

    /**
     * Set/Get Cart has Tax.
     * @param null|boolean $flag
     * @return $this|boolean
     */
    public function hasTax($flag = null)
    {
        if (null === $flag) {
            return $this->session->get('hasTax');
        }

        $this->session->put('hasTax', $flag);

        return $this;
    }

    /**
     * Get the Current Collection for the Prophetoducts.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSession()
    {
        $sessionKey = $this->getSessionKey();

        return $this->session->has($sessionKey) ? $this->session->get($sessionKey) : new Collection;
    }

    /**
     * Get the Current Cart Total
     *
     * @return float $total
     */
    public function total()
    {
        $total = 0.00;
        $cartProducts = $this->getSession();
        foreach ($cartProducts as $product) {
            $total += $product->lineTotal();
        }

        return $total;
    }


    /**
     * Get the Current Cart Tax Total
     *
     * @return float $taxTotal
     */
    public function taxTotal()
    {
        $taxTotal = 0.00;
        $cartProducts = $this->getSession();
        foreach ($cartProducts as $product) {
            $taxTotal += $product->tax();
        }

        return $taxTotal;
    }

    /**
     * Get the Session Key for the Session Manager.
     *
     * @return string $sessionKey
     */
    public function getSessionKey()
    {
        return config('jcart-framework.cart.session_key') ?? 'cart_products';
    }

    /**
     * Get the List of All the Current Session Cart Products.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->getSession();
    }

    /**
     * Get the Total Number of Products into the Cart.
     *
     * @return int $count
     */
    public function count()
    {
        return $this->getSession()->count();
    }
}
