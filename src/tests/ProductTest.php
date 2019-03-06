<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStore()
    {
        $product = factory('App\Product')->make();
        $data = json_decode($product, true);

        $this->post('/api/products', $data);
        $this->assertResponseOK();

        $response = json_decode($this->response->content(), true);

        $this->assertCount(8, $response);

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('free_shipping', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('category_id', $response);
        $this->assertArrayHasKey('updated_at', $response);
        $this->assertArrayHasKey('created_at', $response);

        $this->assertEquals($data['id'], $response['id']);
        $this->assertEquals($data['name'], $response['name']);
        $this->assertEquals($data['free_shipping'], $response['free_shipping']);
        $this->assertEquals($data['description'], $response['description']);
        $this->assertEquals($data['price'], $response['price']);
        $this->assertEquals($data['category_id'], $response['category_id']);

        $this->seeInDatabase('products', $data);
    }

    public function testShow()
    {
        $product = factory('App\Product')->create();
        $data = json_decode($product, true);

        $this->get('/api/products/' . $product->id);
        $this->assertResponseOK();

        $response = json_decode($this->response->content(), true);

        $this->assertCount(8, $response);

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('free_shipping', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('category_id', $response);
        $this->assertArrayHasKey('updated_at', $response);
        $this->assertArrayHasKey('created_at', $response);

        $this->assertEquals($data['id'], $response['id']);
        $this->assertEquals($data['name'], $response['name']);
        $this->assertEquals($data['free_shipping'], $response['free_shipping']);
        $this->assertEquals($data['description'], $response['description']);
        $this->assertEquals($data['price'], $response['price']);
        $this->assertEquals($data['category_id'], $response['category_id']);
    }

    public function testIndex()
    {
        if (count(App\Product::all()) > 0) {
            $product = App\Product::first();
        }
        else {
            $product = factory('App\Product')->create();
        }

        $data = json_decode($product, true);

        $this->get('/api/products');
        $this->assertResponseOK();

        $this->seeJsonStructure([
            '*' => [
                'id', 
                'name', 
                'free_shipping', 
                'description', 
                'price', 
                'category_id', 
                'updated_at', 
                'created_at'
            ]
        ]);

        $response = json_decode($this->response->content(), true);

        $this->assertCount(8, $response[0]);

        $this->assertEquals($data['id'], $response[0]['id']);
        $this->assertEquals($data['name'], $response[0]['name']);
        $this->assertEquals($data['free_shipping'], $response[0]['free_shipping']);
        $this->assertEquals($data['description'], $response[0]['description']);
        $this->assertEquals($data['price'], $response[0]['price']);
        $this->assertEquals($data['category_id'], $response[0]['category_id']);
    }

    public function testUpdate()
    {
        factory('App\Product')->create();
        $product = App\Product::first();
        $curdata = json_decode($product, true);

        $newproduct = factory('App\Product')->make();
        $data = json_decode($newproduct, true);

        $this->put('/api/products/' . $product->id, $data);
        $this->assertResponseOK();

        $response = json_decode($this->response->content(), true);

        $this->assertCount(8, $response);

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('free_shipping', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('category_id', $response);
        $this->assertArrayHasKey('updated_at', $response);
        $this->assertArrayHasKey('created_at', $response);

        $this->assertEquals($product->id, $response['id']);
        $this->assertEquals($data['name'], $response['name']);
        $this->assertEquals($data['free_shipping'], $response['free_shipping']);
        $this->assertEquals($data['description'], $response['description']);
        $this->assertEquals($data['price'], $response['price']);
        $this->assertEquals($data['category_id'], $response['category_id']);

        $this->notSeeInDatabase('products', $curdata);

        $this->seeInDatabase('products', [
            'id' => $product->id,
            'name' => $data['name'],
            'free_shipping' => $data['free_shipping'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
        ]);
    }

    public function testDestroy()
    {
        factory('App\Product')->create();
        $product = App\Product::first();
        $data = json_decode($product, true);

        $this->delete('/api/products/' . $product->id);
        $this->assertResponseOK();

        $this->assertEquals('Removido com sucesso!', $this->response->content());

        $this->notSeeInDatabase('products', $data);
    }
}
