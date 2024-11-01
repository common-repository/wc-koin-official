export function GatewayDescription(attributes) {
    const {sandbox, description} = attributes;
    return (
        <>
            <div className='wc-koin-official-description'>
                <span>{description}</span>
                {sandbox && (
                    <div style={{lineHeight: 1}}>
                        <span style={{opacity: '80%', fontSize: '14px', fontStyle: 'italic'}}>{sandbox}</span>
                    </div>
                )}
            </div>
        </>
    );
}
