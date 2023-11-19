<?php


namespace App\Http\Controllers\API;

use App\Currency;
use App\Http\Controllers\Controller;
use App\Repositories\CurrencyRepository;
use Exception;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    private $currencyRepo;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepo = $currencyRepository;
    }

    public function get(Request $request)
    {
        return self::showResponse(true, $this->currencyRepo->all($request));
    }

    public function find(Request $request, Currency $currency)
    {
        return self::showResponse(true, $this->currencyRepo->all($request));
    }

    public function create(Request $request)
    {
        try {
            $validate = $this->currencyRepo->validate($request, true);
            if ($validate === false) {
                return self::showResponse(false, $validate);
            }
            return self::showResponse(true, $this->currencyRepo->create($request));
        } catch (Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }

    public function update(Request $request, Currency $currency)
    {
        $this->currencyRepo->validate($request, false);
        try {
            $validate = $this->currencyRepo->validate($request, false);
            if ($validate === false) {
                return self::showResponse(false, $validate);
            }
            return self::showResponse(true, $this->currencyRepo->update($request, $currency));
        } catch (Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }

    public function delete(Request $request, Currency $currency)
    {
        try {
            $this->currencyRepo->delete($request, $currency);
            return self::showResponse(true, null);
        } catch (Exception $exception) {
            return self::showResponse(false, $exception->getMessage());
        }
    }
}
