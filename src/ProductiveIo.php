<?php

namespace CuriousMedia;

use GuzzleHttp;

class ProductiveIo
{
	const BASE_URI = 'https://api.productive.io/api/v2/';
	protected $config;
	protected $client;
	public $response = [];

	/**
	 * ProductiveIo constructor.
	 *
	 * @param string $id Productive IO id
	 * @param string $token Productive IO token
	 * @return void
	 */
	public function __construct($id, $token)
	{
		$this->config = array(
			'id' => $id,
			'token' => $token,
		);

		$this->client = new GuzzleHttp\Client([
			'base_uri' => self::BASE_URI
		]);
	}

	/**
	 * Get
	 *
	 * @param string $endpoint
	 * @param array $query
	 * @param array $options see Guzzle request options
	 * @return ProductiveIo
	 */
	public function get($endpoint, $query = [], $options = [])
	{
		$response = $this->client->request('GET', $endpoint, $this->buildOptions([
			'query' => $query
		], $options));

		$this->response[] = json_decode($response->getBody()->getContents());

		return $this;
	}

	/**
	 * Post
	 *
	 * @param string $endpoint
	 * @param array $body
	 * @param array $options see Guzzle request options
	 * @return ProductiveIo
	 */
	public function post($endpoint, $body = [], $options = [])
	{
		$response = $this->client->request('POST', $endpoint, $this->buildOptions([
			GuzzleHttp\RequestOptions::JSON => $body
		], $options));

		$this->response[] = json_decode($response->getBody()->getContents());

		return $this;
	}

	/**
	 * Patch
	 *
	 * @param string $endpoint
	 * @param array $body
	 * @param array $options see Guzzle request options
	 * @return ProductiveIo
	 */
	public function patch($endpoint, $body = [], $options = [])
	{
		$response = $this->client->request('PATCH', $endpoint, $this->buildOptions([
			GuzzleHttp\RequestOptions::JSON => $body
		],$options));

		$this->response[] = json_decode($response->getBody()->getContents());

		return $this;
	}

	/**
	 * Delete
	 *
	 * @param string $endpoint
	 * @param array $body
	 * @param array $options see Guzzle request options
	 * @return ProductiveIo
	 */
	public function delete($endpoint, $body = [], $options = [])
	{
		$response = $this->client->request('DELETE', $endpoint, $this->buildOptions([
			GuzzleHttp\RequestOptions::JSON => $body
		],$options));

		$this->response[] = json_decode($response->getBody()->getContents());

		return $this;
	}

	/**
	 * All
	 *
	 * @return $this
	 */
	public function all()
	{
		$response = end($this->response);

		if (isset($response->links->next) && $response->links->next) {
			$parsed = parse_url($response->links->next);
			parse_str($parsed['query'], $query);

			$this->get($parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'], $query);
			$this->all();
		}

		return $this;
	}

	/**
	 * Result
	 *
	 * 
	 * @param string Key to pull result data
	 * @return mixed
	 */
	public function result($key = null)
	{
		$response = end($this->response);

		if ($key) {
			return $response->{$key};
		}

		return $response;
	}

	/**
	 * Results
	 *
	 * @param string Key to pull result data
	 * @return mixed
	 */
	public function results($key = null)
	{
		$responses = [];

		foreach ($this->response as $response) {
			$responses = array_merge($responses, ($key) ? $response->{$key} : $response);
		}

		return $responses;
	}

	/**
	 * Build options
	 *
	 * @param array $custom
	 * @param array $options
	 * @return array
	 */
	protected function buildOptions($custom, $options)
	{
		return array_merge_recursive(
			$options,
			$custom,
			[
				'headers' => $this->headers()
			]
		);
	}

	/**
	 * Headers
	 *
	 * @return array
	 */
	protected function headers()
	{
		return [
			'X-Organization-Id' => $this->config['id'],
			'X-Auth-Token' => $this->config['token'],
		];
	}
}