async function hit(entity1_id, entity2_id) {
    const hit = await fetch('/process/hit.php', { method: 'POST'
        , headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        , body: `entity1_id=${entity1_id}&entity2_id=${entity2_id}`
     });
    return await hit.json();
}