<?

class waitCallback extends base
{
    static function waitCallbackMethod()
    {
        $args = func_get_args();
        $cacheValueThen=base::get('cachedValueRemainingProcesses');
        $all=base::get();
        #$cacheValueThen=base::i()->get('cachedValueRemainingProcesses');
        $f = preg_replace('~[^a-z0-9]~is', '-', $args) . '.log';
        fpc($f,$cacheValueThen);
    }
}
